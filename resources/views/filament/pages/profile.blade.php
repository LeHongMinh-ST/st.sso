<x-filament::page>
    <!-- Mobile view: tabs on top -->
    <div class="md:hidden w-full mb-6 overflow-x-auto">
        <ul class="fi-sidebar-group-items flex flex-row gap-x-4 pb-2 min-w-max">
            <li class="{{ $activeTab === 'personal' ? 'fi-sidebar-item-active' : '' }} flex-1">
                <a
                    href="javascript:void(0);"
                    wire:click="switchTab('personal')"
                    class="relative flex items-center justify-center gap-x-3 rounded-lg px-3 py-3 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 {{ $activeTab === 'personal' ? 'bg-gray-100 dark:bg-white/5' : '' }} whitespace-nowrap w-full"
                    {{ !$isPasswordChanged ? 'disabled' : '' }}
                >
                    <svg class="h-6 w-6 {{ $activeTab === 'personal' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                    </svg>

                    <span class="flex-1 truncate text-sm font-medium {{ $activeTab === 'personal' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-200' }}">
                        Thông tin cá nhân
                    </span>
                </a>
            </li>

            <li class="{{ $activeTab === 'password' ? 'fi-sidebar-item-active' : '' }} flex-1">
                <a
                    href="javascript:void(0);"
                    wire:click="switchTab('password')"
                    class="relative flex items-center justify-center gap-x-3 rounded-lg px-3 py-3 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 {{ $activeTab === 'password' ? 'bg-gray-100 dark:bg-white/5' : '' }} whitespace-nowrap w-full"
                >
                    <svg class="h-6 w-6 {{ $activeTab === 'password' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>

                    <span class="flex-1 truncate text-sm font-medium {{ $activeTab === 'password' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-200' }}">
                        Đổi mật khẩu
                    </span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Desktop view: sidebar layout -->
    <div class="hidden md:flex gap-4">
        <!-- Menu tab bên trái -->
        <div class="pr-6 border-r">
            <ul class="fi-sidebar-group-items flex flex-col gap-y-3">
                <li class="{{ $activeTab === 'personal' ? 'fi-sidebar-item-active' : '' }} flex-1 md:flex-none">
                    <a
                        href="javascript:void(0);"
                        wire:click="switchTab('personal')"
                        class="relative flex items-center justify-center md:justify-start gap-x-3 rounded-lg px-3 py-3 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 {{ $activeTab === 'personal' ? 'bg-gray-100 dark:bg-white/5' : '' }} whitespace-nowrap md:whitespace-normal w-full"
                        {{ !$isPasswordChanged ? 'disabled' : '' }}
                    >
                        <svg class="h-6 w-6 {{ $activeTab === 'personal' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                        </svg>

                        <span class="flex-1 truncate text-sm font-medium {{ $activeTab === 'personal' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-200' }}">
                            Thông tin cá nhân
                        </span>
                    </a>
                </li>

                <li class="{{ $activeTab === 'password' ? 'fi-sidebar-item-active' : '' }} flex-1 md:flex-none">
                    <a
                        href="javascript:void(0);"
                        wire:click="switchTab('password')"
                        class="relative flex items-center justify-center md:justify-start gap-x-3 rounded-lg px-3 py-3 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 {{ $activeTab === 'password' ? 'bg-gray-100 dark:bg-white/5' : '' }} whitespace-nowrap md:whitespace-normal w-full"
                    >
                        <svg class="h-6 w-6 {{ $activeTab === 'password' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>

                        <span class="flex-1 truncate text-sm font-medium {{ $activeTab === 'password' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-200' }}">
                            Đổi mật khẩu
                        </span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Nội dung tab -->
        <div class="flex-1 ">
            <!-- Tab thông tin cá nhân -->
            @if($activeTab === 'personal')
                <form wire:submit="submitPersonalInfo">
                    {{ $this->personalForm }}

                    <div class="mt-6">
                        <x-filament::button type="submit">
                            Lưu thông tin cá nhân
                        </x-filament::button>
                    </div>
                </form>
            @endif

            <!-- Tab đổi mật khẩu -->
            @if($activeTab === 'password')
                <form wire:submit="submitPasswordChange">
                    {{ $this->passwordForm }}

                    <div class="mt-6">
                        <x-filament::button type="submit">
                            Đổi mật khẩu
                        </x-filament::button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Mobile view: content -->
    <div class="md:hidden">
        <!-- Tab thông tin cá nhân -->
        @if($activeTab === 'personal')
            <form wire:submit="submitPersonalInfo">
                {{ $this->personalForm }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Lưu thông tin cá nhân
                    </x-filament::button>
                </div>
            </form>
        @endif

        <!-- Tab đổi mật khẩu -->
        @if($activeTab === 'password')
            <form wire:submit="submitPasswordChange">
                {{ $this->passwordForm }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Đổi mật khẩu
                    </x-filament::button>
                </div>
            </form>
        @endif
    </div>
</x-filament::page>
