<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-8">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                @livewire(YesterdayTransactions::class)
                @livewire(YesterdayGrossTotal::class)
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
