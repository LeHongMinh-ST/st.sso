<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="p-6 bg-white rounded-xl shadow-sm">
                <h2 class="text-xl font-bold tracking-tight">
                    Xin chào, {{ auth()->user()->full_name }}!
                </h2>
                <p class="mt-2 text-gray-500">
                    Chào mừng bạn đến với hệ thống Single Sign-On của ST Team.
                </p>
            </div>
        </div>

        <div>
            <div class="p-6 bg-white rounded-xl shadow-sm">
                <h2 class="text-xl font-bold tracking-tight">
                    Ứng dụng của bạn
                </h2>
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @php
                        $clients = \App\Models\Client::where('status', \App\Enums\Status::Active)
                            ->where('is_show_dashboard', true)
                            ->get();
                    @endphp

                    @foreach($clients as $client)
                        <a href="{{ $client->redirect }}" target="_blank" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            @if($client->logo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($client->logo) }}" alt="{{ $client->name }}" class="w-12 h-12 rounded-full object-cover mb-2">
                            @else
                                <div class="w-12 h-12 rounded-full bg-primary-500 flex items-center justify-center text-white mb-2">
                                    {{ substr($client->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="text-sm font-medium text-center">{{ $client->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
