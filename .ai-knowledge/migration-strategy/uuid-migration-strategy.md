# UUID Migration Strategy

**Ngày tạo**: 2024-12-14  
**Status**: Proposed  
**Priority**: High

## Vấn đề

Hiện tại database đang sử dụng:
- **Primary Key**: Auto-increment integer (`$table->id()`)
- **Foreign Keys**: `unsignedBigInteger` (`foreignId()`)
- **MySQL**: Database engine

Cần migrate sang UUID để:
- ✅ Phù hợp với DDD architecture (UUID trong Domain layer)
- ✅ Tránh conflicts trong distributed systems
- ✅ Không expose sequential IDs (security)
- ✅ Dễ dàng merge data từ multiple sources

## Phân tích hiện trạng

### Tables hiện tại sử dụng integer ID:

1. **Core Tables**:
   - `users` (id, department_id, faculty_id)
   - `roles` (id)
   - `permissions` (id, permission_group_id)
   - `permission_groups` (id)
   - `departments` (id)
   - `faculties` (id)
   - `clients` (id)

2. **Pivot Tables**:
   - `user_roles` (id, user_id, role_id)
   - `role_permissions` (id, role_id, permission_id)

3. **Laravel System Tables**:
   - `oauth_access_tokens` (id, user_id)
   - `sessions` (id, user_id)
   - `jobs` (id)
   - `notifications` (id, notifiable_id)

### Foreign Key Dependencies:

```
users
├── department_id → departments.id
├── faculty_id → faculties.id
└── (referenced by)
    ├── user_roles.user_id
    ├── oauth_access_tokens.user_id
    ├── sessions.user_id
    └── notifications.notifiable_id

roles
└── (referenced by)
    ├── user_roles.role_id
    └── role_permissions.role_id

permissions
├── permission_group_id → permission_groups.id
└── (referenced by)
    └── role_permissions.permission_id
```

## Migration Strategy: Dual Key Approach (Giữ cả ID cũ và UUID) ✅

**Decision**: **GIỮ CẢ INTEGER ID VÀ UUID** - Không remove integer ID

**Rationale**:
- ✅ **Backward compatible** - Existing code và external systems vẫn hoạt động
- ✅ **Gradual migration** - External systems có thể migrate dần dần
- ✅ **No breaking changes** - Không ảnh hưởng đến systems đang chạy
- ✅ **Flexible** - Có thể dùng cả 2 formats tùy vào use case
- ✅ **Safe** - Có thể rollback nếu cần

**Approach**: 
- **Giữ nguyên** `id` column (integer, primary key)
- **Thêm mới** `uuid` column (CHAR(36) hoặc BINARY(16), unique, indexed)
- **Maintain cả 2** trong long-term
- **Domain layer** sử dụng UUID
- **API layer** có thể return cả 2 IDs
- **External systems** có thể chọn dùng ID hoặc UUID

**Approach**:
1. **Phase 1**: Thêm `uuid` column mới, **GIỮ NGUYÊN** `id` cũ
2. **Phase 2**: Populate UUID cho existing records
3. **Phase 3**: Domain layer sử dụng UUID, API layer support cả 2
4. **Phase 4**: External systems migrate dần dần sang UUID (optional)
5. **Phase 5**: **KHÔNG REMOVE** integer ID - giữ cả 2 vĩnh viễn

**Key Points**:
- ✅ **Integer ID vẫn là Primary Key** - không thay đổi
- ✅ **UUID là additional identifier** - không replace ID
- ✅ **Domain layer dùng UUID** - Value Objects sử dụng UUID
- ✅ **API layer support cả 2** - Backward compatible
- ✅ **External systems tự chọn** - Có thể dùng ID hoặc UUID

**Benefits**:
- ✅ **Zero breaking changes** - Existing systems không bị ảnh hưởng
- ✅ **Gradual migration** - External systems migrate khi sẵn sàng
- ✅ **Rollback safe** - Có thể rollback bất cứ lúc nào
- ✅ **No downtime** - Migration không cần downtime
- ✅ **Testable** - Có thể test từng bước
- ✅ **Flexible** - Support cả 2 formats long-term

---

## Recommended Implementation: Dual Key Approach

### Phase 1: Add UUID Columns

**Migration**: `add_uuid_columns_to_existing_tables.php`

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        // Add uuid column to core tables
        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->index('uuid');
        });

        Schema::table('roles', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->index('uuid');
        });

        Schema::table('permissions', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->index('uuid');
        });

        Schema::table('permission_groups', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->index('uuid');
        });

        Schema::table('departments', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->index('uuid');
        });

        Schema::table('faculties', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->index('uuid');
        });

        Schema::table('clients', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->index('uuid');
        });

        // Add uuid columns to foreign key columns
        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('department_uuid')->nullable()->after('department_id');
            $table->uuid('faculty_uuid')->nullable()->after('faculty_id');
        });

        Schema::table('permissions', function (Blueprint $table): void {
            $table->uuid('permission_group_uuid')->nullable()->after('permission_group_id');
        });

        // Add uuid columns to pivot tables
        Schema::table('user_roles', function (Blueprint $table): void {
            $table->uuid('user_uuid')->nullable()->after('user_id');
            $table->uuid('role_uuid')->nullable()->after('role_id');
        });

        Schema::table('role_permissions', function (Blueprint $table): void {
            $table->uuid('role_uuid')->nullable()->after('role_id');
            $table->uuid('permission_uuid')->nullable()->after('permission_id');
        });
    }

    public function down(): void
    {
        // Remove uuid columns
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['uuid', 'department_uuid', 'faculty_uuid']);
        });
        // ... similar for other tables
    }
};
```

### Phase 2: Populate UUIDs

**Command**: `php artisan migrate:populate-uuids`

```php
<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Client;
use App\SharedKernel\Domain\ValueObjects\Uuid;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class PopulateUuidsCommand extends Command
{
    protected $signature = 'migrate:populate-uuids';
    protected $description = 'Populate UUID columns for existing records';

    public function handle(): int
    {
        $this->info('Populating UUIDs for existing records...');

        DB::transaction(function (): void {
            // Populate core tables
            $this->populateTable(User::class, 'users');
            $this->populateTable(Role::class, 'roles');
            $this->populateTable(Permission::class, 'permissions');
            $this->populateTable(Department::class, 'departments');
            $this->populateTable(Faculty::class, 'faculties');
            $this->populateTable(Client::class, 'clients');

            // Populate foreign key UUIDs
            $this->populateForeignKeys();
        });

        $this->info('UUIDs populated successfully!');

        return Command::SUCCESS;
    }

    private function populateTable(string $modelClass, string $tableName): void
    {
        $this->info("Populating UUIDs for {$tableName}...");

        $modelClass::whereNull('uuid')->chunk(100, function ($records) use ($tableName): void {
            foreach ($records as $record) {
                DB::table($tableName)
                    ->where('id', $record->id)
                    ->update(['uuid' => Uuid::generate()->toString()]);
            }
        });
    }

    private function populateForeignKeys(): void
    {
        // Populate users.department_uuid
        DB::table('users')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->whereNull('users.department_uuid')
            ->update([
                'users.department_uuid' => DB::raw('departments.uuid'),
            ]);

        // Similar for other foreign keys...
    }
}
```

### Phase 3: Make UUID Required và Unique

**Migration**: `make_uuid_required_and_unique.php`

```php
public function up(): void
{
    // Make uuid required và unique
    Schema::table('users', function (Blueprint $table): void {
        $table->uuid('uuid')->nullable(false)->unique()->change();
    });
    // ... similar for other tables
}
```

### Phase 4: Update Code Gradually

**Strategy**:
1. **New DDD Code**: Sử dụng UUID từ đầu
2. **Old Code**: Vẫn sử dụng integer ID
3. **Bridge**: Tạo bridge methods để convert giữa ID và UUID

**Example Bridge**:
```php
// In Eloquent Model
public function getUuidAttribute(): string
{
    return $this->attributes['uuid'] ?? 
           DB::table('users')->where('id', $this->id)->value('uuid');
}

// In Repository
public function findById(Uuid $uuid): ?User
{
    $eloquentUser = User::where('uuid', $uuid->toString())->first();
    // Convert to Domain User...
}
```

### Phase 5: Remove Integer IDs (After Full Migration)

**Migration**: `remove_integer_ids.php`

```php
public function up(): void
{
    // Drop foreign key constraints first
    Schema::table('users', function (Blueprint $table): void {
        $table->dropForeign(['department_id']);
        $table->dropForeign(['faculty_id']);
    });

    // Drop integer columns
    Schema::table('users', function (Blueprint $table): void {
        $table->dropColumn(['id', 'department_id', 'faculty_id']);
        $table->renameColumn('uuid', 'id');
        $table->primary('id');
    });

    // Recreate foreign keys với UUID
    Schema::table('users', function (Blueprint $table): void {
        $table->foreign('department_uuid')->references('uuid')->on('departments');
        $table->foreign('faculty_uuid')->references('uuid')->on('faculties');
    });
}
```

---

## Performance Considerations

### UUID Index Size

- **Integer (BIGINT)**: 8 bytes
- **UUID (CHAR(36))**: 36 bytes
- **UUID (BINARY(16))**: 16 bytes (recommended)

**Recommendation**: Sử dụng `BINARY(16)` thay vì `CHAR(36)` để optimize performance:

```php
$table->uuid('uuid')->binary()->nullable();
```

### Index Strategy

- ✅ Index UUID columns ngay sau khi add
- ✅ Composite indexes cho foreign keys: `(uuid, created_at)`
- ✅ Consider partial indexes nếu cần

---

## Migration Checklist

### Pre-Migration

- [ ] Backup database
- [ ] Test migration trên staging environment
- [ ] Estimate downtime (nếu cần)
- [ ] Document rollback procedure

### Migration Steps

- [ ] **Step 1**: Add UUID columns (nullable)
- [ ] **Step 2**: Populate UUIDs cho existing records
- [ ] **Step 3**: Verify data integrity
- [ ] **Step 4**: Make UUID required và unique
- [ ] **Step 5**: Update code gradually
- [ ] **Step 6**: Monitor performance
- [ ] **Step 7**: Remove integer IDs (sau khi code đã migrate hoàn toàn)

### Post-Migration

- [ ] Verify all queries use UUID
- [ ] Monitor performance metrics
- [ ] Update documentation
- [ ] Remove migration scripts (sau khi stable)

---

## Recommendations

### ✅ Recommended Approach: Dual Key (Strategy 2)

**Reasons**:
1. **Safe**: Không breaking existing functionality
2. **Gradual**: Có thể migrate từng phần
3. **Testable**: Có thể test từng bước
4. **Rollback**: Có thể rollback nếu cần

### ⚠️ Important Notes

1. **Don't rush**: Migration này cần thời gian, đừng vội
2. **Test thoroughly**: Test trên staging trước khi production
3. **Monitor performance**: UUID indexes lớn hơn, cần monitor
4. **Document everything**: Document mọi thay đổi
5. **Coordinate with team**: Đảm bảo team biết về migration

### 📋 Timeline Estimate

- **Phase 1** (Add UUID columns): 1-2 giờ
- **Phase 2** (Populate UUIDs): 1-2 giờ (depends on data size)
- **Phase 3** (Make UUID required): 30 phút
- **Phase 4** (Update code): 2-4 tuần (gradual)
- **Phase 5** (Remove integer IDs): 1-2 giờ (sau khi code đã migrate)

**Total**: ~3-4 tuần (với gradual code migration)

---

## Alternative: Keep Integer IDs

Nếu UUID migration quá phức tạp, có thể:

1. **Keep integer IDs** trong database
2. **Use UUID only in Domain layer** (Value Objects)
3. **Map UUID ↔ ID** trong Repository layer

**Pros**:
- ✅ Không cần database migration
- ✅ Performance tốt hơn (integer indexes)
- ✅ Simpler migration

**Cons**:
- ⚠️ UUID không phải là "source of truth"
- ⚠️ Cần maintain mapping logic
- ⚠️ Không đúng với DDD principles (ID trong Domain layer)

---

## Decision

**Recommended**: **Dual Key Approach (Strategy 2)**

**Timeline**: Implement trong Phase 2 (OrganizationalStructure Context) khi migrate User model.

**Next Steps**:
1. Review strategy này với team
2. Create detailed migration scripts
3. Test trên staging environment
4. Schedule migration window
