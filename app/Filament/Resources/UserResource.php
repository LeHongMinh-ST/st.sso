<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Role as RoleEnum;
use App\Enums\Status;
use App\Filament\Resources\UserResource\Actions\ResetPasswordAction;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Faculty;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Người dùng';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationGroup = 'Hệ thống';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sử dụng Tabs cho toàn bộ form với layout sidebar
                Forms\Components\Tabs::make('main_tabs')
                    ->contained(false)
                    ->extraAttributes([
                        'class' => 'filament-profile-tabs',
                        'x-data' => "{ activeTab: 'Thông tin người dùng' }",
                    ])
                    ->tabs([
                        // Tab thông tin người dùng
                        Forms\Components\Tabs\Tab::make('Thông tin người dùng')
                            ->icon('heroicon-o-user')
                            ->schema([
                                // Thông tin cá nhân
                                Forms\Components\Section::make('Thông tin cá nhân')
                                    ->schema([
                                        Forms\Components\TextInput::make('user_name')
                                            ->label('Tên đăng nhập')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('first_name')
                                            ->label('Tên')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('last_name')
                                            ->label('Họ')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('password')
                                            ->label('Mật khẩu')
                                            ->password()
                                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                            ->dehydrated(fn (?string $state): bool => filled($state))
                                            ->required(fn (string $operation): bool => 'create' === $operation),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Số điện thoại')
                                            ->tel()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('code')
                                            ->label('Mã')
                                            ->maxLength(255),
                                    ])->columns(2),

                                // Thông tin cơ bản
                                Forms\Components\Section::make('Thông tin cơ bản')
                                    ->schema([
                                        Forms\Components\Select::make('role')
                                            ->label('Loại tài khoản')
                                            ->options(RoleEnum::getDescription())
                                            ->required(),
                                        Forms\Components\Select::make('faculty_id')
                                            ->label('Khoa')
                                            ->relationship('faculty', 'name')
                                            ->options(Faculty::pluck('name', 'id'))
                                            ->searchable(),
                                        Forms\Components\Select::make('status')
                                            ->label('Trạng thái')
                                            ->options(Status::getDescription())
                                            ->required(),
                                        Forms\Components\Toggle::make('is_change_password')
                                            ->label('Đã thay đổi mật khẩu')
                                            ->default(false),
                                    ])->columns(2),
                            ]),

                        // Tab phân quyền
                        Forms\Components\Tabs\Tab::make('Phân quyền')
                            ->icon('heroicon-o-shield-check')
                            ->badge(fn (User $record) => $record->roles()->count())
                            ->schema([
                                Forms\Components\Section::make('Vai trò và quyền hạn')
                                    ->description('Người dùng có thể được gán nhiều vai trò. Mỗi vai trò có các quyền khác nhau.')
                                    ->headerActions([
                                        // Nút chọn tất cả các vai trò
                                        Forms\Components\Actions\Action::make('select_all_roles')
                                            ->label('Chọn tất cả vai trò')
                                            ->icon('heroicon-o-check-circle')
                                            ->color('success')
                                            ->action(function (Forms\Set $set): void {
                                                // Lấy tất cả ID của vai trò
                                                $allRoleIds = Role::pluck('id')->toArray();
                                                $set('roles', $allRoleIds);
                                            }),

                                        // Nút bỏ chọn tất cả các vai trò
                                        Forms\Components\Actions\Action::make('deselect_all_roles')
                                            ->label('Bỏ chọn tất cả')
                                            ->icon('heroicon-o-x-circle')
                                            ->color('danger')
                                            ->action(function (Forms\Set $set): void {
                                                $set('roles', []);
                                            }),
                                    ])
                                    ->schema([
                                        Forms\Components\CheckboxList::make('roles')
                                            ->label('Vai trò')
                                            ->relationship('roles', 'name')
                                            ->options(
                                                Role::query()
                                                    ->orderBy('name')
                                                    ->get()
                                                    ->mapWithKeys(function (Role $role) {
                                                        $permissionCount = $role->permissions()->count();
                                                        $status = $role->status->getLabel();
                                                        return [$role->id => "[{$role->code}] {$role->name} - {$status} ({$permissionCount} quyền)"];
                                                    })
                                            )
                                            ->descriptions(
                                                Role::query()
                                                    ->orderBy('name')
                                                    ->get()
                                                    ->mapWithKeys(function (Role $role) {
                                                        $permissionGroups = $role->permissions()
                                                            ->join('permission_groups', 'permissions.group_code', '=', 'permission_groups.code')
                                                            ->select('permission_groups.name')
                                                            ->distinct()
                                                            ->pluck('name')
                                                            ->implode(', ');

                                                        $desc = $role->description;
                                                        if (!empty($permissionGroups)) {
                                                            $desc .= "\nNhóm quyền: {$permissionGroups}";
                                                        }

                                                        return [$role->id => $desc];
                                                    })
                                                    ->toArray()
                                            )
                                            ->columns(2)
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->gridDirection('row')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Nhóm cột họ tên và ảnh đại diện
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->extraAttributes(['class' => 'filament-user-avatar']),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Họ tên')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(
                        query: fn (\Illuminate\Database\Eloquent\Builder $query, string $direction): \Illuminate\Database\Eloquent\Builder =>
                        $query->orderBy('first_name', $direction)
                    )
                    ->description(fn (User $record): string => $record->email)
                    ->wrap()
                    ->extraAttributes(['class' => 'filament-user-info']),
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Tên đăng nhập')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('role')
                    ->label('Loại tài khoản')
                    ->formatStateUsing(fn (RoleEnum $state): string => $state->getLabel())
                    ->badge()
                    ->color(fn (RoleEnum $state): string => match ($state) {
                        RoleEnum::SuperAdmin => 'danger',
                        RoleEnum::Officer => 'warning',
                        RoleEnum::Student => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('roles')
                    ->label('Vai trò')
                    ->formatStateUsing(fn (User $record) => '')
                    ->description(function (User $record): string {
                        $roles = $record->roles;

                        if ($roles->isEmpty()) {
                            return '';
                        }

                        // Hiển thị 2 vai trò đầu tiên
                        $visibleRoles = $roles->take(2)->pluck('name')->implode(', ');

                        // Nếu có nhiều hơn 2 vai trò, thêm dấu ...
                        if ($roles->count() > 2) {
                            $visibleRoles .= '...';
                        }

                        return $visibleRoles;
                    })
                    ->tooltip(function (User $record): ?string {
                        $roles = $record->roles;

                        if ($roles->count() <= 2) {
                            return null; // Không cần tooltip nếu ít hơn hoặc bằng 2 vai trò
                        }

                        // Hiển thị tất cả vai trò trong tooltip
                        return $roles->pluck('name')->implode(', ');
                    }),
                Tables\Columns\TextColumn::make('faculty.name')
                    ->label('Khoa')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_change_password')
                    ->label('Đã thay đổi mật khẩu')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn (Status $state): string => $state->getLabel())
                    ->badge()
                    ->color(fn (Status $state): string => match ($state) {
                        Status::Active => 'success',
                        Status::Inactive => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('search')
                    ->form([
                        Forms\Components\TextInput::make('search')
                            ->label('Tìm kiếm')
                            ->placeholder('Tìm theo tên, email, tên đăng nhập...')
                            ->columnSpan(2),
                    ])
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder => $query
                        ->when(
                            $data['search'],
                            fn (\Illuminate\Database\Eloquent\Builder $query, $search): \Illuminate\Database\Eloquent\Builder =>
                            $query->where(fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder => $query
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('user_name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%"))
                        )),
                Tables\Filters\SelectFilter::make('role')
                    ->label('Loại tài khoản')
                    ->options(RoleEnum::getDescription())
                    ->preload(),
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Vai trò')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('faculty_id')
                    ->label('Khoa')
                    ->relationship('faculty', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Status::getDescription())
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Chỉnh sửa')
                        ->icon('heroicon-o-pencil-square'),
                    Tables\Actions\Action::make('assign_roles')
                        ->label('Phân quyền')
                        ->icon('heroicon-o-shield-check')
                        ->url(fn (User $record): string => static::getUrl('edit', ['record' => $record]) . '?activeTab=phân-quyền'),
                    Tables\Actions\Action::make('view_activities')
                        ->label('Xem hoạt động')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->url(fn (User $record): string => route('filament.sso.resources.activity-logs.index', ['tableFilters[user_id][value]' => $record->id])),
                    ResetPasswordAction::make('reset_password'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa')
                        ->icon('heroicon-o-trash'),
                ])
                    ->label('Hành động')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa các mục đã chọn')
                        ->icon('heroicon-o-trash'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
