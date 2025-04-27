<x-filament::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Tạo bản sao lưu mới</x-slot>
            
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">
                        Tạo một bản sao lưu mới của cơ sở dữ liệu. Quá trình này có thể mất vài phút tùy thuộc vào kích thước dữ liệu.
                    </p>
                </div>
                
                <x-filament::button wire:click="createBackup" wire:loading.attr="disabled" color="primary">
                    <span wire:loading.remove wire:target="createBackup">
                        Tạo bản sao lưu
                    </span>
                    <span wire:loading wire:target="createBackup">
                        Đang tạo...
                    </span>
                </x-filament::button>
            </div>
        </x-filament::section>
        
        <x-filament::section>
            <x-slot name="heading">Danh sách bản sao lưu</x-slot>
            
            @if(empty($this->getBackups()))
                <div class="flex items-center justify-center p-6 text-gray-500 dark:text-gray-400">
                    Chưa có bản sao lưu nào được tạo.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tên file
                                </th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Kích thước
                                </th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Ngày tạo
                                </th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->getBackups() as $backup)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $backup['name'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $backup['size'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $backup['date'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex space-x-2">
                                            <x-filament::button wire:click="downloadBackup('{{ $backup['path'] }}')" size="sm" color="success">
                                                Tải xuống
                                            </x-filament::button>
                                            
                                            <x-filament::button wire:click="deleteBackup('{{ $backup['path'] }}')" size="sm" color="danger">
                                                Xóa
                                            </x-filament::button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament::page>
