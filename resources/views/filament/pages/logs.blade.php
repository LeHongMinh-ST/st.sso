<x-filament::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="md:col-span-1 space-y-4">
            <x-filament::section>
                <x-slot name="heading">Danh sách nhật ký</x-slot>
                
                <div class="space-y-2">
                    @foreach($this->getLogs() as $log)
                        <button 
                            wire:click="selectLog('{{ $log['name'] }}')"
                            class="w-full text-left px-4 py-2 rounded-lg transition {{ $selectedLog && $selectedLog['name'] === $log['name'] ? 'bg-primary-500 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        >
                            <div class="font-medium">{{ $log['name'] }}</div>
                            <div class="text-xs {{ $selectedLog && $selectedLog['name'] === $log['name'] ? 'text-white/80' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $log['modified'] }} - {{ $log['size'] }}
                            </div>
                        </button>
                    @endforeach
                </div>
            </x-filament::section>
            
            @if($selectedLog)
                <div class="flex space-x-2">
                    <x-filament::button wire:click="clearLog" color="danger" class="w-full">
                        Xóa
                    </x-filament::button>
                    
                    <x-filament::button wire:click="downloadLog" color="gray" class="w-full">
                        Tải xuống
                    </x-filament::button>
                </div>
            @endif
        </div>
        
        <div class="md:col-span-3">
            <x-filament::section>
                <x-slot name="heading">
                    @if($selectedLog)
                        {{ $selectedLog['name'] }}
                    @else
                        Nội dung nhật ký
                    @endif
                </x-slot>
                
                @if($selectedLog)
                    <div class="bg-gray-800 text-gray-200 p-4 rounded-lg overflow-auto max-h-[70vh] font-mono text-xs">
                        @if(empty($logContent))
                            <div class="text-center py-4 text-gray-400">Không có dữ liệu</div>
                        @else
                            <pre>{{ $logContent }}</pre>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 text-gray-400">Chọn một file nhật ký để xem nội dung</div>
                @endif
            </x-filament::section>
        </div>
    </div>
</x-filament::page>
