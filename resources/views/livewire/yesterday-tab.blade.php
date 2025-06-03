<x-filament-widgets::widget>
    <x-filament::section>
        {{-- @dd($data) --}}
        <div class="grid col-span-2 grid-flow-col gap-4">
            <div class="col-span-1">
                @livewire(YesterdayTransactions::class,['total_transactions' => $data['total_transaction']])
            </div>
            <div class="col-span-1">
                @livewire(YesterdayGrossTotal::class,['total_sales' => $data['total_sales']])
            </div>
        </div><br>
        <div class="grid grid-flow-col grid-cols-5 gap-4">
            <div class="col-span-3">
                @livewire(YesterdaySales::class)
            </div>
            <div class="col-span-2">
                @livewire(YesterdayIncome::class,['chartData' => $data['income']])
            </div>
        </div><br>
        <div class="grid col-span-2 grid-flow-col gap-4">
            <div class="col-span-1">
                <div class="w-full rounded-md border border-gray-300 dark:border-gray-700">
                    <h2 class="font-semibold text-lg py-2 px-3">Product Trends</h2>

                    <table class="w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th style="width: 40%" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-700">Product</th>
                                <th style="width: 40%" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-700">Image</th>
                                <th style="width: 20%" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-700">Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach ($data['product_trend'] as $productTrend)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100 border-r border-gray-300 dark:border-gray-700">{{$productTrend['name'] ?? null}}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100 border-r border-gray-300 dark:border-gray-700">
                                        @if ($productTrend['image_path'])
                                            <img class="h-16 w-24 object-contain" src="{{$productTrend['image_path']}}" />
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100 border-r border-gray-300 dark:border-gray-700">{{$productTrend['quantity'] ?? null}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-span-1">
                <div class="w-full rounded-md border border-gray-300 dark:border-gray-700">
                    <h2 class="font-semibold text-lg py-2 px-3">Best Employee</h2>

                    <table class="w-full divide-y divide-gray-300 dark:divide-gray-700">
                      <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th style="width: 70%" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-700">Employee Name</th>
                            <th style="width: 30%" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-700">Earnings</th>
                        </tr>
                      </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach ($data['best_employee'] as $bestEmp)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100 border-r border-gray-300 dark:border-gray-700">{{$bestEmp->processedBy?->name ?? 'N/A'}}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100 border-r border-gray-300 dark:border-gray-700">₱ {{number_format($bestEmp?->total,2) ?? 0.00}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
