<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Role as RoleEnum;
use App\Enums\Status;
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
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Tên đăng nhập')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Họ tên')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('roles_count')
                    ->label('Số vai trò')
                    ->counts('roles')
                    ->badge()
                    ->description(fn (User $record): string => $record->roles->pluck('name')->implode(', ')),
                Tables\Columns\TextColumn::make('faculty.name')
                    ->label('Khoa')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_change_password')
                    ->label('Đã thay đổi mật khẩu')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('role')
                    ->label('Loại tài khoản')
                    ->options(RoleEnum::getDescription()),
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Vai trò')
                    ->relationship('roles', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('faculty_id')
                    ->label('Khoa')
                    ->relationship('faculty', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Status::getDescription()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Chỉnh sửa')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
                Tables\Actions\Action::make('assign_roles')
                    ->label('Phân quyền')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->url(fn (User $record): string => static::getUrl('edit', ['record' => $record]) . '?activeTab=phân-quyền'),
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
