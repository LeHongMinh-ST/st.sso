<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    // Resource này chỉ được sử dụng trong code, không hiển thị trong menu
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin quyền hạn')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên quyền hạn')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Mã quyền hạn')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('group_code')
                            ->label('Nhóm quyền')
                            ->options(PermissionGroup::pluck('name', 'code'))
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên quyền hạn')
                    ->description(fn (Permission $record): string => "[{$record->code}]")
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã quyền hạn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('Nhóm quyền')
                    ->description(fn (Permission $record): string => "[{$record->group_code}]")
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group_code')
                    ->label('Nhóm quyền')
                    ->options(PermissionGroup::pluck('name', 'code')),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
