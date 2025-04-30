<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class OnlineUsers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Theo dõi người dùng';

    protected static ?string $title = 'Theo dõi người dùng';

    protected static ?string $slug = 'online-users';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationGroup = 'Ứng dụng';

    protected static string $view = 'filament.pages.online-users';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereExists(function ($query): void {
                        $query->select(DB::raw(1))
                            ->from('sessions')
                            ->whereColumn('sessions.user_id', 'users.id')
                            ->where('sessions.last_activity', '>=', now()->subMinutes(5)->getTimestamp());
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Tên đăng nhập')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Họ tên')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Vai trò')
                    ->formatStateUsing(fn ($state): string => $state->getLabel())
                    ->badge()
                    ->color(fn ($state): string => match ($state->value) {
                        1 => 'danger',
                        2 => 'warning',
                        3 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('faculty.name')
                    ->label('Khoa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Hoạt động cuối')
                    ->getStateUsing(function (User $record) {
                        $session = DB::table('sessions')
                            ->where('user_id', $record->id)
                            ->orderBy('last_activity', 'desc')
                            ->first();

                        if ($session) {
                            return now()->createFromTimestamp($session->last_activity)->diffForHumans();
                        }

                        return null;
                    }),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->getStateUsing(function (User $record) {
                        $session = DB::table('sessions')
                            ->where('user_id', $record->id)
                            ->orderBy('last_activity', 'desc')
                            ->first();

                        if ($session) {
                            return $session->ip_address;
                        }

                        return null;
                    }),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_activities')
                        ->label('Xem nhật ký hoạt động')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->color('info')
                        ->url(fn (User $record): string => route('filament.sso.resources.logs.index', ['tableFilters[user_id][value]' => $record->id])),

                    Tables\Actions\Action::make('logout')
                        ->label('Đăng xuất')
                        ->color('danger')
                        ->icon('heroicon-o-arrow-left-on-rectangle')
                        ->action(function (User $record): void {
                            DB::table('sessions')
                                ->where('user_id', $record->id)
                                ->delete();

                            Notification::make()
                                ->success()
                                ->title('Đã đăng xuất người dùng')
                                ->send();
                        }),
                ])
                ->label('Hành động')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('logout_selected')
                    ->label('Đăng xuất đã chọn')
                    ->color('danger')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->action(function ($records): void {
                        $userIds = $records->pluck('id')->toArray();

                        DB::table('sessions')
                            ->whereIn('user_id', $userIds)
                            ->delete();

                        Notification::make()
                            ->success()
                            ->title('Đã đăng xuất các người dùng đã chọn')
                            ->send();
                    }),
            ])
            ->poll('10s');
    }
}
