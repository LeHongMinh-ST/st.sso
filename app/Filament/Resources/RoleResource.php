<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Resources\RoleResource\Pages;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Vai trò & Quyền hạn';

    protected static ?int $navigationSort = 60;

    protected static ?string $navigationGroup = 'Hệ thống';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin vai trò')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên vai trò')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Mã vai trò')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options(Status::getDescription())
                            ->default(Status::Active->value)
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Quyền hạn')
                    ->headerActions([
                        // Nút chọn tất cả các quyền
                        Forms\Components\Actions\Action::make('select_all_permissions')
                            ->label('Chọn tất cả quyền')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->action(function (Forms\Set $set): void {
                                // Lấy tất cả ID của quyền
                                $allPermissionIds = Permission::pluck('id')->toArray();
                                $set('permissions', $allPermissionIds);
                            }),

                        // Nút bỏ chọn tất cả các quyền
                        Forms\Components\Actions\Action::make('deselect_all_permissions')
                            ->label('Bỏ chọn tất cả')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                            ->action(function (Forms\Set $set): void {
                                $set('permissions', []);
                            }),
                    ])
                    ->schema([
                        // Nhóm quyền và quyền hạn
                        Forms\Components\Grid::make()
                            ->schema(
                                function () {
                                    $schema = [];
                                    $permissionGroups = PermissionGroup::with('permissions')
                                        ->orderBy('name')
                                        ->get();

                                    foreach ($permissionGroups as $group) {
                                        // Tạo một section cho mỗi nhóm quyền
                                        $schema[] = Forms\Components\Section::make("[{$group->code}] {$group->name} ({$group->permissions->count()})")
                                            ->description($group->description)
                                            ->collapsible()
                                            ->icon('heroicon-o-folder')
                                            ->schema([
                                                // Danh sách các quyền trong nhóm
                                                Forms\Components\CheckboxList::make('permissions')
                                                    ->label('')
                                                    ->bulkToggleable()
                                                    ->relationship('permissions', 'name')
                                                    ->options(
                                                        $group->permissions
                                                            ->mapWithKeys(fn (Permission $permission) => [$permission->id => "[{$permission->code}] {$permission->name}"])
                                                            ->toArray()
                                                    )
                                                    ->columns(2)
                                            ])
                                            ->columnSpanFull();
                                    }

                                    return $schema;
                                }
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên vai trò')
                    ->description(fn (Role $record): string => "[{$record->code}]")
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã vai trò')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (Status $state): string => $state->getLabel())
                    ->color(fn (Status $state): string => match ($state) {
                        Status::Active => 'success',
                        Status::Inactive => 'danger',
                    }),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Số quyền hạn')
                    ->counts('permissions')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Chỉnh sửa')
                        ->icon('heroicon-o-pencil-square'),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
