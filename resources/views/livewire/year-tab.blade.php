<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid col-span-2 grid-flow-col gap-4">
            <div class="col-span-1">
                @livewire(YearlyTransactions::class)
            </div>
            <div class="col-span-1">
                @livewire(YearlyGrossTotal::class)
            </div>
        </div><br>
        <div class="grid grid-flow-col grid-cols-5 gap-4">
            <div class="col-span-3">
                @livewire(YearlySales::class)
            </div>
            <div class="col-span-2">
                @livewire(YearlyIncome::class)
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
