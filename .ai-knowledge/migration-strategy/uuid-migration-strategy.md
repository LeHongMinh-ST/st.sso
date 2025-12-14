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

### Phase 5: Long-term Maintenance (Giữ cả 2 IDs)

**Decision**: **KHÔNG REMOVE INTEGER ID** - Giữ cả 2 vĩnh viễn

**Rationale**:
- External systems có thể không migrate
- Integer ID vẫn có performance benefits
- Giữ cả 2 cho flexibility
- No breaking changes

**Maintenance**:
- ✅ Integer ID vẫn là Primary Key
- ✅ UUID là unique identifier
- ✅ Domain layer sử dụng UUID
- ✅ API layer support cả 2
- ✅ External systems tự chọn format

**Note**: Nếu trong tương lai muốn remove integer ID, có thể làm sau khi:
- Tất cả external systems đã migrate
- Đã có deprecation period đủ dài
- Đã có approval từ stakeholders

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

### ✅ Recommended Approach: Dual Key - Giữ cả 2 IDs

**Decision**: **GIỮ CẢ INTEGER ID VÀ UUID VĨNH VIỄN**

**Reasons**:
1. **Zero breaking changes**: Existing systems không bị ảnh hưởng
2. **Flexible**: Support cả 2 formats
3. **Safe**: Có thể rollback bất cứ lúc nào
4. **Gradual**: External systems migrate khi sẵn sàng
5. **Performance**: Integer ID vẫn có performance benefits

### ⚠️ Important Notes

1. **Integer ID vẫn là Primary Key**: Không thay đổi database structure
2. **UUID là additional identifier**: Không replace ID
3. **Domain layer dùng UUID**: Value Objects sử dụng UUID
4. **API layer support cả 2**: Backward compatible
5. **External systems tự chọn**: Có thể dùng ID hoặc UUID
6. **Test thoroughly**: Test trên staging trước khi production
7. **Monitor performance**: UUID indexes lớn hơn, cần monitor
8. **Document everything**: Document mọi thay đổi
9. **Coordinate with external teams**: Đảm bảo external systems biết về UUID option

### 📋 Timeline Estimate

- **Phase 1** (Add UUID columns): 1-2 giờ
- **Phase 2** (Populate UUIDs): 1-2 giờ (depends on data size)
- **Phase 3** (Make UUID required và unique): 30 phút
- **Phase 4** (Update Domain layer code): 1-2 tuần
- **Phase 5** (API compatibility layer): 1 tuần
- **Phase 6** (External systems migration - optional): Ongoing

**Total**: ~2-3 tuần cho internal migration, external systems migrate khi sẵn sàng

---

## Final Decision

**Approach**: **Dual Key - Giữ cả Integer ID và UUID**

**Key Points**:
- ✅ **Integer ID**: Vẫn là Primary Key, không thay đổi
- ✅ **UUID**: Thêm mới, unique identifier, indexed
- ✅ **Domain Layer**: Sử dụng UUID (Value Objects)
- ✅ **API Layer**: Support cả 2 formats
- ✅ **External Systems**: Tự chọn dùng ID hoặc UUID
- ✅ **Long-term**: Giữ cả 2 vĩnh viễn (không remove integer ID)

**Benefits**:
- Zero breaking changes
- Backward compatible
- Flexible cho external systems
- Safe và rollback-able
- Performance benefits từ integer ID

**Timeline**: Implement trong Phase 2 (OrganizationalStructure Context) khi migrate User model.

**Next Steps**:
1. ✅ Review strategy này với team
2. ✅ Create detailed migration scripts
3. ✅ Test trên staging environment
4. ✅ Communicate với external systems về UUID option
5. ✅ Implement trong Phase 2
