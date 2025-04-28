<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Enums\Status;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Faculty;
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
                Forms\Components\Section::make('Thông tin người dùng')
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
                Forms\Components\Section::make('Phân quyền')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Vai trò')
                            ->options(Role::getDescription())
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
                    ->label('Vai trò')
                    ->formatStateUsing(fn (Role $state): string => $state->getLabel())
                    ->badge()
                    ->color(fn (Role $state): string => match ($state) {
                        Role::SuperAdmin => 'danger',
                        Role::Officer => 'warning',
                        Role::Student => 'success',
                        default => 'gray',
                    }),
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
                    ->label('Vai trò')
                    ->options(Role::getDescription()),
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
