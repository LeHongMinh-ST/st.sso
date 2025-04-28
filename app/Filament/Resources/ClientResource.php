<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Enums\Status;
use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static ?string $navigationLabel = 'Ứng dụng';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationGroup = 'Ứng dụng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin ứng dụng')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên ứng dụng')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('id')
                            ->label('Client ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => null !== $record),
                        Forms\Components\TextInput::make('secret')
                            ->label('Client Secret')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => null !== $record),
                        Forms\Components\TextInput::make('redirect')
                            ->label('Redirect URI')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('Mô tả')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('logo')
                            ->label('Logo URL')
                            ->maxLength(255),
                        Forms\Components\CheckboxList::make('allowed_roles')
                            ->label('Vai trò được phép')
                            ->options(Role::getDescription())
                            ->columns(2),
                        Forms\Components\Toggle::make('is_show_dashboard')
                            ->label('Hiển thị trên dashboard')
                            ->default(true),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options(Status::getDescription())
                            ->default(Status::Active)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên ứng dụng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id')
                    ->label('Client ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('redirect')
                    ->label('Redirect URI')
                    ->limit(30),
                Tables\Columns\TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(30),
                Tables\Columns\IconColumn::make('is_show_dashboard')
                    ->label('Hiển thị trên dashboard')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn ($state): string => $state instanceof Status ? $state->getLabel() : 'Active')
                    ->badge()
                    ->color(fn ($state): string => $state instanceof Status ? match ($state) {
                        Status::Active => 'success',
                        Status::Inactive => 'danger',
                        default => 'gray',
                    } : 'success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Status::getDescription()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Chỉnh sửa')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
