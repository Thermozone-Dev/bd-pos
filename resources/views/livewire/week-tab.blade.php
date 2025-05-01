<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid col-span-2 grid-flow-col gap-4">
            <div class="col-span-1">
                @livewire(WeeklyTransactions::class)
            </div>
            <div class="col-span-1">
                @livewire(WeeklyGrossTotal::class)
            </div>
        </div><br>
        <div class="grid grid-flow-col grid-cols-5 gap-4">
            <div class="col-span-3">
                @livewire(WeeklySales::class)
            </div>
            <div class="col-span-2">
                @livewire(WeeklyIncome::class)
            </div>
        </div><br>
        <div class="grid col-span-2 grid-flow-col gap-4">
            <div class="col-span-1">
                @livewire(TrendingProducts::class)
            </div>
            <div class="col-span-1">
                @livewire(BestEmployees::class)
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
