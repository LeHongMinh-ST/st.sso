<div class="p-6 bg-white rounded-xl shadow-sm dark:bg-gray-800">
    <div class="flex items-center space-x-4">
        <div class="p-3 bg-primary-100 rounded-full dark:bg-primary-900">
            <x-heroicon-o-hand-raised class="w-8 h-8 text-primary-500" />
        </div>
        
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ $this->getCurrentTime() }}, {{ $this->getUserName() }}!
            </h2>
            
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Chào mừng bạn đến với hệ thống ST Single Sign-On. Bạn có thể quản lý tài khoản và ứng dụng từ đây.
            </p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-3">
        <a href="{{ route('filament.sso.resources.users.index') }}" class="flex items-center p-4 transition bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <div class="mr-4 flex-shrink-0">
                <x-heroicon-o-users class="w-6 h-6 text-primary-500" />
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Quản lý người dùng</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Xem và quản lý tài khoản người dùng</p>
            </div>
        </a>
        
        <a href="{{ route('filament.sso.resources.clients.index') }}" class="flex items-center p-4 transition bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <div class="mr-4 flex-shrink-0">
                <x-heroicon-o-window class="w-6 h-6 text-primary-500" />
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Quản lý ứng dụng</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Xem và quản lý các ứng dụng</p>
            </div>
        </a>
        
        <a href="{{ route('filament.sso.pages.online-users') }}" class="flex items-center p-4 transition bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <div class="mr-4 flex-shrink-0">
                <x-heroicon-o-user-group class="w-6 h-6 text-primary-500" />
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Theo dõi người dùng</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Xem người dùng đang trực tuyến</p>
            </div>
        </a>
    </div>
</div>
