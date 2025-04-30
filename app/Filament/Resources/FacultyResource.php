<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Resources\FacultyResource\Pages;
use App\Filament\Resources\FacultyResource\RelationManagers;
use App\Models\Faculty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FacultyResource extends Resource
{
    protected static ?string $model = Faculty::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Khoa';

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationGroup = 'Hệ thống';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin khoa')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên khoa')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('Mô tả')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options(Status::getDescription())
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên khoa')
                    ->searchable(),

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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Status::getDescription()),
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
            RelationManagers\DepartmentsRelationManager::class,
            RelationManagers\TeachersRelationManager::class,
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaculties::route('/'),
            'create' => Pages\CreateFaculty::route('/create'),
            'edit' => Pages\EditFaculty::route('/{record}/edit'),
        ];
    }
}
