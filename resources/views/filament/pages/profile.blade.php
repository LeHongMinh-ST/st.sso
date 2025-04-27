<x-filament::page>
    <form wire:submit="submit">
        {{ $this->form }}
        
        <div class="mt-6">
            <x-filament::button type="submit">
                Lưu thay đổi
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
