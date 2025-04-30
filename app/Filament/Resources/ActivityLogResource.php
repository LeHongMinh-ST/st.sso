<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Nhật ký hệ thống';

    protected static ?string $slug = 'logs';

    protected static ?int $navigationSort = 70;

    protected static ?string $navigationGroup = 'Hệ thống';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin nhật ký')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Người dùng')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Forms\Components\TextInput::make('action')
                            ->label('Hành động')
                            ->disabled(),
                        Forms\Components\TextInput::make('model_type')
                            ->label('Loại đối tượng')
                            ->disabled(),
                        Forms\Components\TextInput::make('model_id')
                            ->label('ID đối tượng')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Địa chỉ IP')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_agent')
                            ->label('Trình duyệt')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->disabled(),
                        Forms\Components\KeyValue::make('before')
                            ->label('Dữ liệu trước khi thay đổi')
                            ->disabled(),
                        Forms\Components\KeyValue::make('after')
                            ->label('Dữ liệu sau khi thay đổi')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Người dùng')
                    ->description(fn (ActivityLog $record): ?string => $record->user ? "{$record->user->full_name}" : null)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Hành động')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'create' => 'Tạo mới',
                        'update' => 'Cập nhật',
                        'delete' => 'Xóa',
                        'login' => 'Đăng nhập',
                        'logout' => 'Đăng xuất',
                        default => Str::title($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        'login' => 'info',
                        'logout' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('model_type')
                    ->label('Loại đối tượng')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? class_basename($state) : null),
                Tables\Columns\TextColumn::make('model_id')
                    ->label('ID đối tượng'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(50),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Người dùng')
                    ->options(User::query()->pluck('email', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('action')
                    ->label('Hành động')
                    ->options([
                        'create' => 'Tạo mới',
                        'update' => 'Cập nhật',
                        'delete' => 'Xóa',
                        'login' => 'Đăng nhập',
                        'logout' => 'Đăng xuất',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Từ ngày'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Đến ngày'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        )),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Không cần bulk actions
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}
