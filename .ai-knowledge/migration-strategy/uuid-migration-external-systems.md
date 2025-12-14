# UUID Migration Strategy - External Systems Integration

**Ngày tạo**: 2024-12-14  
**Status**: Critical - Cần giải quyết trước migration  
**Priority**: High

## Vấn đề

Có các hệ thống khác đang:
- Lưu integer IDs từ hệ thống này (SSO)
- Sử dụng IDs này để reference users/entities
- Có thể đang call APIs với integer IDs
- Có thể có foreign keys hoặc references trong database của họ

**Khi migrate sang UUID**:
- ❌ External systems sẽ không tìm thấy entities nếu chỉ search bằng integer ID
- ❌ APIs sẽ break nếu chỉ accept UUID
- ❌ Data consistency sẽ bị ảnh hưởng
- ❌ Cần coordinate migration với multiple systems

## Phân tích Impact

### Scenarios

#### Scenario 1: External System lưu User ID trong database của họ

```
External System Database:
┌─────────────────────┐
│ orders              │
├─────────────────────┤
│ id                  │
│ user_id (INT)       │ ← Reference đến SSO system
│ order_date          │
└─────────────────────┘
```

**Impact**: 
- External system cần update `user_id` từ integer sang UUID
- Hoặc cần maintain mapping table

#### Scenario 2: External System call API với integer ID

```
GET /api/users/123  ← Integer ID
```

**Impact**:
- API endpoint sẽ break nếu chỉ accept UUID
- Cần backward compatibility

#### Scenario 3: External System có foreign key constraints

**Impact**:
- Database constraints sẽ break
- Cần coordinate migration timing

## Giải pháp: Multi-Phase Migration với Backward Compatibility

### Strategy: Dual Key + API Versioning + Mapping Service

**Approach**: 
1. **Maintain cả integer ID và UUID** trong transition period
2. **API Versioning** để support cả 2 formats
3. **Mapping Service** để convert giữa ID và UUID
4. **Gradual migration** cho external systems

---

## Phase 1: Preparation - API Compatibility Layer

### 1.1: Create ID Mapping Service

**Purpose**: Convert giữa integer ID và UUID

```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Services;

use App\SharedKernel\Domain\ValueObjects\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Service to map between integer IDs and UUIDs.
 * Used during migration period to maintain backward compatibility.
 */
final class IdMappingService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get UUID from integer ID.
     *
     * @param string $tableName
     * @param int $integerId
     * @return string|null
     */
    public function getUuidFromIntegerId(string $tableName, int $integerId): ?string
    {
        $cacheKey = "id_mapping:{$tableName}:{$integerId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tableName, $integerId) {
            return DB::table($tableName)
                ->where('id', $integerId)
                ->value('uuid');
        });
    }

    /**
     * Get integer ID from UUID.
     *
     * @param string $tableName
     * @param string $uuid
     * @return int|null
     */
    public function getIntegerIdFromUuid(string $tableName, string $uuid): ?int
    {
        $cacheKey = "id_mapping:{$tableName}:{$uuid}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tableName, $uuid) {
            return DB::table($tableName)
                ->where('uuid', $uuid)
                ->value('id');
        });
    }

    /**
     * Resolve identifier (supports both integer ID and UUID).
     *
     * @param string $tableName
     * @param string|int $identifier
     * @return array{id: int|null, uuid: string|null}
     */
    public function resolveIdentifier(string $tableName, string|int $identifier): array
    {
        if (is_int($identifier) || ctype_digit((string) $identifier)) {
            // Integer ID provided
            $integerId = (int) $identifier;
            $uuid = $this->getUuidFromIntegerId($tableName, $integerId);

            return [
                'id' => $integerId,
                'uuid' => $uuid,
            ];
        }

        // UUID provided
        $uuid = $identifier;
        $integerId = $this->getIntegerIdFromUuid($tableName, $uuid);

        return [
            'id' => $integerId,
            'uuid' => $uuid,
        ];
    }
}
```

### 1.2: API Versioning Strategy

**Approach**: Support cả v1 (integer ID) và v2 (UUID)

#### Option A: URL-based Versioning

```
GET /api/v1/users/123          ← Integer ID (legacy)
GET /api/v2/users/{uuid}       ← UUID (new)
```

#### Option B: Accept Both Formats (Recommended)

```
GET /api/users/123             ← Integer ID (backward compatible)
GET /api/users/{uuid}          ← UUID (new format)
```

**Implementation**:

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\SharedKernel\Infrastructure\Services\IdMappingService;
use App\OrganizationalStructure\Application\UseCases\FindUserUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserController extends Controller
{
    public function __construct(
        private readonly FindUserUseCase $findUserUseCase,
        private readonly IdMappingService $idMappingService
    ) {
    }

    /**
     * Show user - supports both integer ID and UUID.
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        // Resolve identifier (supports both formats)
        $mapping = $this->idMappingService->resolveIdentifier('users', $identifier);

        if (!$mapping['uuid']) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        // Use UUID to find user in Domain layer
        $user = $this->findUserUseCase->execute(
            Uuid::fromString($mapping['uuid'])
        );

        // Return response with both IDs for backward compatibility
        return response()->json([
            'id' => $mapping['id'],        // Integer ID (legacy)
            'uuid' => $mapping['uuid'],   // UUID (new)
            'data' => [
                // User data...
            ],
        ]);
    }
}
```

### 1.3: Route Pattern để Accept Both

```php
// routes/api.php

// Support both integer ID and UUID
Route::get('/users/{identifier}', [UserController::class, 'show'])
    ->where('identifier', '[0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
```

---

## Phase 2: External Systems Migration Support

### 2.1: Migration API Endpoint

**Purpose**: Provide migration endpoint để external systems có thể query mapping

```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Http\Controllers;

use App\SharedKernel\Infrastructure\Services\IdMappingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MigrationController extends Controller
{
    public function __construct(
        private readonly IdMappingService $idMappingService
    ) {
    }

    /**
     * Get UUID mapping for integer IDs.
     * 
     * POST /api/migration/map-ids
     * {
     *   "table": "users",
     *   "ids": [1, 2, 3, 4, 5]
     * }
     */
    public function mapIds(Request $request): JsonResponse
    {
        $request->validate([
            'table' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $mappings = [];
        foreach ($request->ids as $id) {
            $uuid = $this->idMappingService->getUuidFromIntegerId(
                $request->table,
                $id
            );

            $mappings[] = [
                'id' => $id,
                'uuid' => $uuid,
            ];
        }

        return response()->json([
            'mappings' => $mappings,
        ]);
    }

    /**
     * Bulk export ID mappings for external systems.
     * 
     * GET /api/migration/export-mappings?table=users&limit=1000
     */
    public function exportMappings(Request $request): JsonResponse
    {
        $request->validate([
            'table' => 'required|string',
            'limit' => 'integer|max:10000',
        ]);

        $limit = $request->input('limit', 1000);
        $table = $request->input('table');

        $mappings = DB::table($table)
            ->select('id', 'uuid')
            ->whereNotNull('uuid')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'uuid' => $row->uuid,
            ]);

        return response()->json([
            'table' => $table,
            'count' => $mappings->count(),
            'mappings' => $mappings,
        ]);
    }
}
```

### 2.2: Migration Documentation cho External Systems

**File**: `docs/api/migration-guide.md`

```markdown
# API Migration Guide - Integer ID to UUID

## Overview

SSO system đang migrate từ integer IDs sang UUIDs. Tài liệu này hướng dẫn các external systems migrate theo.

## Timeline

- **Phase 1** (Week 1-2): Dual support - cả integer ID và UUID đều được accept
- **Phase 2** (Week 3-4): External systems migrate sang UUID
- **Phase 3** (Week 5+): Deprecate integer ID support

## Migration Steps cho External Systems

### Step 1: Update Database Schema

Thêm UUID column vào tables có reference đến SSO system:

```sql
ALTER TABLE orders ADD COLUMN user_uuid CHAR(36) NULL AFTER user_id;
CREATE INDEX idx_orders_user_uuid ON orders(user_uuid);
```

### Step 2: Populate UUIDs

Call migration API để get UUID mappings:

```bash
curl -X POST https://sso.example.com/api/migration/map-ids \
  -H "Content-Type: application/json" \
  -d '{
    "table": "users",
    "ids": [1, 2, 3, 4, 5]
  }'
```

Response:
```json
{
  "mappings": [
    {"id": 1, "uuid": "550e8400-e29b-41d4-a716-446655440000"},
    {"id": 2, "uuid": "6ba7b810-9dad-11d1-80b4-00c04fd430c8"},
    ...
  ]
}
```

Update database:
```sql
UPDATE orders o
JOIN user_mappings m ON o.user_id = m.id
SET o.user_uuid = m.uuid;
```

### Step 3: Update API Calls

**Before**:
```php
GET /api/users/123
```

**After**:
```php
GET /api/users/550e8400-e29b-41d4-a716-446655440000
```

Hoặc vẫn có thể dùng integer ID trong transition period:
```php
GET /api/users/123  // Still works during Phase 1
```

### Step 4: Update Code

**Before**:
```php
$userId = 123;
$user = $ssoApi->getUser($userId);
```

**After**:
```php
$userUuid = '550e8400-e29b-41d4-a716-446655440000';
$user = $ssoApi->getUser($userUuid);
```

### Step 5: Verify và Test

- ✅ Test với UUID format
- ✅ Verify data consistency
- ✅ Test error handling

### Step 6: Remove Integer ID Support

Sau khi đã migrate hoàn toàn:
- Remove `user_id` column
- Update foreign keys
- Remove mapping logic

## API Changes

### Endpoints Support Both Formats

All endpoints now accept both integer ID and UUID:

```
GET /api/users/{identifier}
  - identifier can be: 123 (integer) or {uuid}
  
POST /api/users/{identifier}/roles
  - Same as above
```

### Response Format

Responses include both IDs during transition:

```json
{
  "id": 123,
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "data": {
    "name": "John Doe",
    ...
  }
}
```

## Migration Checklist cho External Systems

- [ ] Review impact analysis
- [ ] Plan migration timeline
- [ ] Update database schema
- [ ] Populate UUIDs
- [ ] Update API calls
- [ ] Update application code
- [ ] Test thoroughly
- [ ] Deploy to staging
- [ ] Verify data consistency
- [ ] Deploy to production
- [ ] Monitor for issues
- [ ] Remove integer ID support (after deprecation period)

## Support

Nếu có vấn đề trong quá trình migration:
- Email: devops@example.com
- Slack: #sso-migration
- Documentation: https://docs.example.com/api/migration
```

---

## Phase 3: Coordination Strategy

### 3.1: Communication Plan

**Stakeholders**:
- SSO System Team
- External System Teams (cần identify tất cả)
- DevOps Team
- Product Owners

**Communication Channels**:
- Email notifications
- Slack channel (#sso-migration)
- Weekly sync meetings
- Migration dashboard

### 3.2: Migration Schedule

**Timeline**: 6-8 weeks total

```
Week 1-2: Preparation
├── SSO System: Add UUID columns
├── SSO System: Populate UUIDs
├── SSO System: Deploy API compatibility layer
└── External Systems: Review migration guide

Week 3-4: External Systems Migration
├── External System A: Migrate
├── External System B: Migrate
└── External System C: Migrate

Week 5-6: Verification
├── Verify all systems migrated
├── Monitor for issues
└── Fix any problems

Week 7-8: Deprecation
├── Deprecate integer ID support
├── Remove integer ID columns (if needed)
└── Final cleanup
```

### 3.3: Rollback Plan

**If migration fails**:
1. **SSO System**: Revert API changes (keep integer ID support)
2. **External Systems**: Revert code changes
3. **Database**: Keep UUID columns (không drop ngay)

**Rollback triggers**:
- Critical bugs không thể fix nhanh
- Data inconsistency issues
- Performance degradation > 20%

---

## Phase 4: Monitoring và Support

### 4.1: Migration Dashboard

**Metrics to track**:
- API calls với integer ID vs UUID
- External systems migration status
- Error rates
- Performance metrics

**Dashboard**:
```php
// Admin dashboard để track migration progress
Route::get('/admin/migration-status', [MigrationStatusController::class, 'index']);
```

### 4.2: Deprecation Warnings

**API Response Headers**:
```php
// When using integer ID (deprecated)
X-API-Deprecated: true
X-API-Deprecation-Date: 2024-12-31
X-API-Sunset-Date: 2025-03-31
X-Migration-Guide: https://docs.example.com/api/migration
```

**Logging**:
- Log tất cả API calls với integer ID
- Alert nếu có external system chưa migrate sau deprecation date

---

## Recommendations

### ✅ Recommended Approach

1. **Dual Key + API Compatibility** (Phase 1-2)
   - Maintain cả integer ID và UUID
   - API accept cả 2 formats
   - Provide migration endpoints

2. **Gradual Migration** (Phase 3)
   - External systems migrate từng cái một
   - Coordinate với từng team
   - Monitor và support

3. **Deprecation Period** (Phase 4)
   - 2-3 months deprecation period
   - Clear communication
   - Support cho external systems

### ⚠️ Important Considerations

1. **Identify All External Systems**
   - List tất cả systems đang integrate
   - Contact từng team
   - Document dependencies

2. **Data Consistency**
   - Ensure UUIDs được populate đúng
   - Verify mappings
   - Test thoroughly

3. **Performance**
   - Cache ID mappings
   - Optimize queries
   - Monitor performance

4. **Communication**
   - Early communication với external teams
   - Clear migration guide
   - Support channel

---

## Alternative: Keep Integer IDs Externally

Nếu migration quá phức tạp, có thể:

1. **Keep integer IDs trong API responses**
   - Return cả `id` và `uuid`
   - External systems có thể chọn dùng cái nào

2. **Internal UUID, External Integer ID**
   - Domain layer dùng UUID
   - API layer convert UUID → integer ID khi response
   - External systems không cần migrate

**Pros**:
- ✅ External systems không cần migrate
- ✅ Simpler migration

**Cons**:
- ⚠️ UUID không phải là "source of truth" trong API
- ⚠️ Cần maintain conversion logic
- ⚠️ Không đúng với DDD principles

---

## Decision Matrix

| Approach | Complexity | External Impact | Recommended |
|----------|-----------|----------------|-------------|
| Dual Key + API Compatibility | Medium | Low (gradual) | ✅ Yes |
| Keep Integer IDs Externally | Low | None | ⚠️ Consider |
| Full UUID Migration | High | High (breaking) | ❌ No |

---

## Next Steps

1. **Identify External Systems**
   - [ ] List tất cả systems đang integrate
   - [ ] Contact từng team
   - [ ] Document dependencies

2. **Create Migration Plan**
   - [ ] Timeline cho từng external system
   - [ ] Communication plan
   - [ ] Support plan

3. **Implement Compatibility Layer**
   - [ ] IdMappingService
   - [ ] API compatibility
   - [ ] Migration endpoints

4. **Communicate với External Teams**
   - [ ] Send migration guide
   - [ ] Schedule meetings
   - [ ] Provide support

---

**Last Updated**: 2024-12-14  
**Status**: Proposed - Cần review với team và external stakeholders
