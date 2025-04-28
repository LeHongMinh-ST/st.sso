<?php

declare(strict_types=1);

namespace App\Filament\Resources\FacultyResource\RelationManagers;

use App\Enums\Role;
use App\Enums\Status;
use App\Imports\StudentsImport;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $title = 'Sinh viên';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin sinh viên')
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
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Mã sinh viên')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => 'create' === $operation),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options(Status::getDescription())
                            ->default(Status::Active)
                            ->required(),
                        Forms\Components\Hidden::make('role')
                            ->default(Role::Student->value),
                        Forms\Components\Hidden::make('is_change_password')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->modifyQueryUsing(fn ($query) => $query->where('role', Role::Student->value))
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
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã sinh viên')
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Status::getDescription()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm sinh viên')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['role'] = Role::Student->value;
                        return $data;
                    }),
                Tables\Actions\Action::make('import')
                    ->label('Import')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required(),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('download_template')
                                ->label('Tải xuống file mẫu')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('gray')
                                ->url(route('templates.students'))
                                ->openUrlInNewTab()
                        ])
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        try {
                            $file = Storage::disk('public')->path($data['file']);
                            Excel::import(new StudentsImport($livewire->getOwnerRecord()->id), $file);

                            Notification::make()
                                ->title('Import thành công')
                                ->success()
                                ->send();

                            // Xóa file sau khi import
                            Storage::disk('public')->delete($data['file']);

                            // Refresh lại danh sách
                            $livewire->refreshRecords();
                        } catch (ValidationException $e) {
                            Notification::make()
                                ->title('Import thất bại')
                                ->body('Có lỗi trong file Excel: ' . implode(', ', array_map(fn ($error) => implode(', ', $error), $e->failures())))
                                ->danger()
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Import thất bại')
                                ->body('Có lỗi xảy ra: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Chỉnh sửa')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
                Tables\Actions\Action::make('reset_password')
                    ->label('Đặt lại mật khẩu')
                    ->color('warning')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->update([
                            'password' => Hash::make('password'),
                            'is_change_password' => false,
                        ]);

                        $this->notification()->success(
                            title: 'Đặt lại mật khẩu thành công',
                            body: 'Mật khẩu đã được đặt lại thành "password"'
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa các mục đã chọn')
                        ->icon('heroicon-o-trash'),
                    Tables\Actions\BulkAction::make('reset_multiple_passwords')
                        ->label('Đặt lại mật khẩu')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(function ($record): void {
                                $record->update([
                                    'password' => Hash::make('password'),
                                    'is_change_password' => false,
                                ]);
                            });

                            $this->notification()->success(
                                title: 'Đặt lại mật khẩu thành công',
                                body: 'Mật khẩu của các tài khoản đã được đặt lại thành "password"'
                            );
                        }),
                ]),
            ]);
    }
}
