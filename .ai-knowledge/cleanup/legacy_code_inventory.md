# Legacy Code Inventory - Phase 6 Cleanup

**Ngày tạo**: 2024  
**Mục đích**: Document tất cả legacy code cần cleanup trong Phase 6

## Status Legend

- ✅ **Safe to Remove**: Code không còn được sử dụng, có thể xóa an toàn
- ⚠️ **Still in Use**: Code vẫn đang được sử dụng, cần migrate trước khi remove
- 🔄 **Migrated but Kept**: Code đã được migrate nhưng vẫn giữ lại cho backward compatibility
- ❓ **Needs Review**: Cần review kỹ trước khi quyết định

---

## Legacy Controllers

### API Controllers (`app/Http/Controllers/Api/`)

#### ✅ `AuthenticateSSOController.php` - **REMOVED**
- **Status**: Empty class, không được sử dụng
- **Replacement**: `App\IdentityAccess\Infrastructure\Http\Controllers\Api\AuthenticateSSOController`
- **Action**: ✅ Đã xóa

#### 🔄 `UserController.php` - **LEGACY ROUTES**
- **Status**: Vẫn đang được sử dụng trong `routes/api.php` như "legacy-users" routes
- **Replacement**: `App\OrganizationalStructure\Infrastructure\Http\Controllers\UserController`
- **Routes**: 
  - `legacy-users` (index, show, store)
  - `legacy-users/{user}/reset-password`
- **Action**: ⚠️ Giữ lại cho backward compatibility, có thể deprecate sau

#### 🔄 `FacultyController.php` - **LEGACY ROUTES**
- **Status**: Vẫn đang được sử dụng trong `routes/api.php` như "legacy-faculties" routes
- **Replacement**: `App\OrganizationalStructure\Infrastructure\Http\Controllers\FacultyController`
- **Routes**:
  - `legacy-faculties` (index)
  - `legacy-faculties/get-all`
  - `legacy-faculties/{faculty}/users`
  - `legacy-faculties/{faculty}/teachers`
  - `legacy-faculties/{faculty}/departments`
- **Action**: ⚠️ Giữ lại cho backward compatibility, có thể deprecate sau

### Admin Controllers (`app/Http/Controllers/Admin/`)

#### ⚠️ `UserController.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong `routes/web.php`
- **Replacement**: Cần migrate sang DDD controllers
- **Routes**: `/users/*` (index, create, show, edit)
- **Action**: ❓ Cần migrate trước khi remove

#### ⚠️ `FacultyController.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong `routes/web.php`
- **Replacement**: Cần migrate sang DDD controllers
- **Routes**: `/faculties/*` (index, create, show, edit)
- **Action**: ❓ Cần migrate trước khi remove

#### ⚠️ `ClientController.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong `routes/web.php`
- **Replacement**: Cần migrate sang DDD controllers
- **Routes**: `/clients/*` (index, create, show, edit)
- **Action**: ❓ Cần migrate trước khi remove

#### ⚠️ `RoleController.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong `routes/web.php`
- **Replacement**: Cần migrate sang DDD controllers
- **Routes**: `/roles/*` (index, create, show, edit)
- **Action**: ❓ Cần migrate trước khi remove

#### ⚠️ `UserRoleController.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong `routes/web.php`
- **Replacement**: Cần migrate sang DDD controllers
- **Routes**: `/users/{user}/roles` (edit)
- **Action**: ❓ Cần migrate trước khi remove

#### ⚠️ `DashboardController.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong `routes/web.php`
- **Replacement**: Có thể giữ lại hoặc migrate
- **Routes**: `/` (dashboard)
- **Action**: ❓ Cần review

#### ⚠️ `ProfileController.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong `routes/web.php`
- **Replacement**: Có thể giữ lại hoặc migrate
- **Routes**: `/profile` (index)
- **Action**: ❓ Cần review

### Auth Controllers (`app/Http/Controllers/Auth/`)

#### ⚠️ `AuthenticateController.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong `routes/web.php`
- **Replacement**: `App\IdentityAccess\Infrastructure\Http\Controllers\AuthenticateController`
- **Routes**: `/login`, `/logout`, `/authorize/azure/*`
- **Action**: ❓ Cần verify và migrate nếu cần

---

## Legacy Models (`app/Models/`)

### ⚠️ `User.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng ở nhiều nơi
- **Replacement**: `App\OrganizationalStructure\Infrastructure\Eloquent\User`
- **Usage**: 
  - Livewire components
  - Admin controllers
  - Policies
  - Seeders
- **Action**: ❓ Cần migrate tất cả references trước khi remove

### ⚠️ `Faculty.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng ở nhiều nơi
- **Replacement**: `App\OrganizationalStructure\Infrastructure\Eloquent\Faculty`
- **Usage**:
  - Livewire components
  - Admin controllers
  - Policies
- **Action**: ❓ Cần migrate tất cả references trước khi remove

### ⚠️ `Department.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng ở nhiều nơi
- **Replacement**: `App\OrganizationalStructure\Infrastructure\Eloquent\Department`
- **Usage**:
  - Livewire components
  - Admin controllers
- **Action**: ❓ Cần migrate tất cả references trước khi remove

### ⚠️ `Role.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng ở nhiều nơi
- **Replacement**: `App\IdentityAccess\Infrastructure\Eloquent\Role`
- **Usage**:
  - Livewire components
  - Admin controllers
  - Policies
  - Seeders
- **Action**: ❓ Cần migrate tất cả references trước khi remove

### ⚠️ `Permission.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng ở nhiều nơi
- **Replacement**: `App\IdentityAccess\Infrastructure\Eloquent\Permission`
- **Usage**:
  - Policies
  - Seeders
- **Action**: ❓ Cần migrate tất cả references trước khi remove

### ⚠️ `Client.php` - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng ở nhiều nơi
- **Replacement**: `App\IdentityAccess\Infrastructure\Eloquent\Client`
- **Usage**:
  - Livewire components
  - Admin controllers
  - Policies
- **Action**: ❓ Cần migrate tất cả references trước khi remove

### ❓ `PermissionGroup.php` - **NEEDS REVIEW**
- **Status**: Cần verify usage
- **Replacement**: Có thể không có trong DDD
- **Action**: ❓ Cần review

---

## Legacy Livewire Components (`app/Livewire/`)

### ⚠️ Tất cả Livewire Components - **STILL IN USE**
- **Status**: Vẫn đang được sử dụng trong web routes
- **Action**: ❓ Đã refactor để sử dụng PolicyAuthorizationService, nhưng vẫn cần giữ lại

---

## Summary

### Safe to Remove Now
- ✅ `app/Http/Controllers/Api/AuthenticateSSOController.php` - **REMOVED**

### Can Remove After Migration
- Legacy API controllers (`UserController`, `FacultyController`) - sau khi deprecate legacy routes
- Legacy Admin controllers - sau khi migrate sang DDD controllers
- Legacy Models - sau khi migrate tất cả references

### Keep for Now
- Legacy routes (backward compatibility)
- Livewire components (đã refactor, vẫn cần giữ lại)
- Models (cần cho database migrations và Eloquent relationships)

---

## Next Steps

1. ✅ Remove empty `AuthenticateSSOController` - **DONE**
2. ⏳ Migrate Admin controllers sang DDD (nếu cần)
3. ⏳ Deprecate legacy API routes (thêm deprecation headers)
4. ⏳ Update documentation về legacy code
5. ⏳ Plan removal timeline cho legacy code
