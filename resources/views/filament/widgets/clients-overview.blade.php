<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center">
            <x-heroicon-o-window class="w-5 h-5 mr-2 text-primary-500" />
            <span>Ứng dụng của bạn</span>
        </div>
    </x-slot>

    @if(count($this->getClients()) > 0)
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($this->getClients() as $client)
                <div class="flex flex-col h-full overflow-hidden transition-all duration-200 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center p-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-full bg-primary-50 dark:bg-primary-900 flex-shrink-0">
                            @if(isset($client->logo) && $client->logo)
                                <img src="{{ $client->logo }}" alt="{{ $client->name }}" class="w-8 h-8 rounded-full">
                            @elseif(isset($client->thumbnail) && $client->thumbnail)
                                <img src="{{ $client->thumbnail }}" alt="{{ $client->name }}" class="w-8 h-8 rounded-full">
                            @else
                                <x-heroicon-o-window class="w-5 h-5 text-primary-500" />
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium truncate text-gray-900 dark:text-white">{{ $client->name }}</h3>
                        </div>
                    </div>

                    <div class="flex-1 p-4">
                        @if(isset($client->description) && $client->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $client->description }}</p>
                        @else
                            <p class="text-xs text-gray-400 dark:text-gray-500 italic">Không có mô tả</p>
                        @endif
                    </div>

                    <div class="p-4 pt-0">
                        <a href="{{ $client->redirect }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-600 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                            <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 mr-1" />
                            Truy cập ứng dụng
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center p-6 text-center">
            <div class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-800">
                <x-heroicon-o-window class="w-8 h-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Không có ứng dụng nào</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Hiện tại chưa có ứng dụng nào được cấu hình để hiển thị trên dashboard.
            </p>
            <a href="{{ route('filament.sso.resources.clients.index') }}" class="mt-4 inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-600 dark:focus:ring-offset-gray-800">
                Quản lý ứng dụng
            </a>
        </div>
    @endif
</x-filament::section>
