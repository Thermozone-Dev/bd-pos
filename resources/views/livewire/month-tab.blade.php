<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid col-span-2 grid-flow-col gap-4">
            <div class="col-span-1">
                @livewire(MonthlyTransactions::class)
            </div>
            <div class="col-span-1">
                @livewire(MonthlyGrossTotal::class)
            </div>
        </div><br>
        <div class="grid grid-flow-col grid-cols-5 gap-4">
            <div class="col-span-3">
                @livewire(MonthlySales::class)
            </div>
            <div class="col-span-2">
                @livewire(MonthlyIncome::class)
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
