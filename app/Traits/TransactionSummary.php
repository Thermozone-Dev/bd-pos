<?php

namespace App\Traits;

use App\Models\Transaction;
use App\Models\TransactionBasketItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait TransactionSummary
{
    /**
     * Generate a report based on the specified filter.
     *
     * @param string $filter
     * @return void
     */

    public function transactionSummary($filter)
    {
        // Switch statement to handle different filter cases

        switch ($filter) {
            case 'all':
                $transaction = Transaction::withoutGlobalScopes()
                    ->where('is_valid', true);
                return $this->getData($transaction);
                break;

            case 'yesterday':
                $transaction = Transaction::withoutGlobalScopes()
                    ->where('is_valid', true)
                    ->whereDate('created_at', Carbon::yesterday());
                return $this->getData($transaction);
                break;

            case 'today':
                $transaction = Transaction::withoutGlobalScopes()
                    ->where('is_valid', true)
                    ->whereDate('created_at', Carbon::today());
                    return $this->getData($transaction);
                break;

            case 'week':
                $transaction = Transaction::withoutGlobalScopes()
                    ->where('is_valid', true)
                    ->whereBetween('created_at', [Carbon::now()->startOfWeek()->startOfDay(),Carbon::now()->endOfWeek()->endOfDay()]);
                    return $this->getData($transaction);
                break;

            case 'month':
                $transaction = Transaction::withoutGlobalScopes()
                    ->where('is_valid', true)
                    ->whereMonth('created_at', Carbon::now()->month);
                    return $this->getData($transaction);
                break;

            case 'year':
                    $transaction = Transaction::withoutGlobalScopes()
                        ->where('is_valid', true)
                        ->whereYear('created_at', Carbon::now()->year);
                    return $this->getData($transaction);

            case $filter:
                    $transaction = Transaction::withoutGlobalScopes()
                        ->where('is_valid', true)
                        ->whereYear('created_at', Carbon::parse($filter));
                    return $this->getData($transaction);
                break;

            default:
                return 'Add filter (today, yesterday, week, month, year) to the reportForTransaction method';
        }
    }


    public function getData($transaction){
        DB::statement("SET SQL_MODE=''");


        $bestEmployee = Transaction::select('processed_by', DB::raw('SUM(total_sales) as total'))
            ->groupBy('processed_by')
            ->whereIn('id',$transaction->pluck('id'))
            ->where('is_valid', true)
            ->orderByDesc('total')
            ->take(8)
            ->get();

        //get transaction basket items
        $basketItems = TransactionBasketItem::whereIn('transaction_basket_id', $transaction->pluck('transaction_basket_id'))->get();

        //get bought products
        $products = collect();
        $categories = collect();

        $packages = collect();

        $products_excluded_in_package = collect();

        $detailed_products = collect();
        $detailed_packages = collect();

        foreach($basketItems as $basketItem){
            $basketTransaction = Transaction::withoutGlobalScopes()
                ->where('transaction_basket_id', $basketItem->transaction_basket_id)
                ->first();

            $basketUser = $basketTransaction->processedBy->id;
            $basketDate = Carbon::parse($basketTransaction->created_at)->format('Y-m-d');

            if($basketItem->item->product){

                // get quantity per product package included
                $quantity = $basketItem->quantity ?? 1;

                $index = $products->search(fn ($item) => $item['product_id'] === $basketItem->item->product->id);

                if ($index !== false) {
                    $item = $products->get($index);
                    $item['quantity'] += $quantity;
                    $products->put($index, $item);
                } else {
                    $products->push([
                        'product_id' => $basketItem->item->product->id,
                        'name' => $basketItem->item->product->name,
                        'image_path' => $basketItem->item->product->getMedia()?->first()?->getUrl() ?? asset('images/pos-default.jpg'),
                        'quantity' => $quantity,
                    ]);
                }

                // get category income
                $index = $categories->search(fn ($test) => $test['id'] === $basketItem->item->product->productType->id);

                if ($index !== false) {
                    $item = $categories->get($index);
                    $item['income'] += $basketItem->total_value;
                    $categories->put($index, $item);
                } else {
                    $categories->push([
                        'id' => $basketItem->item->product->productType->id,
                        'name' => $basketItem->item->product->productType->name,
                        'income' => $basketItem->total_value ?? 0
                    ]);
                }


                //get all products that not included in package

                $index = $products_excluded_in_package->search(fn ($item) => $item['product_id'] === $basketItem->item->product->id);

                if ($index !== false) {
                    $item = $products_excluded_in_package->get($index);
                    $item['quantity'] += $quantity;
                    $item['gross_income'] += $basketItem->total_value;
                    $item['income'] += $basketItem->total_value + ($basketItem->total_value*.12);
                    $products_excluded_in_package->put($index, $item);
                } else {
                    $products_excluded_in_package->push([
                        'product_id' => $basketItem->item->product->id,
                        'name' => $basketItem->item->product->name,
                        'price' => $basketItem->item->product->price,
                        'image_path' => $basketItem->item->product->getMedia()?->first()?->getUrl() ?? asset('images/pos-default.jpg'),
                        'quantity' => $quantity,
                        'gross_income' => $basketItem->total_value,
                        'income' => $basketItem->total_value + ($basketItem->total_value*.12) ?? 0,
                    ]);
                }

                // $index = $detailed_products->search(fn ($item) => $item['product_id'] === $basketItem->item->product->id && $item['user'] === $basketUser && $item['date'] === $basketDate);
                // if ($index !== false){
                //     $item = $detailed_products->get($index);
                //     $item['quantity'] += $quantity;
                //     $item['gross_income'] += $basketItem->total_value;
                //     $detailed_products->put($index, $item);
                // } else{

                //     $detailed_products->push([
                //         'product_id' => $basketItem->item->product->id,
                //         'user' => $basketUser,
                //         'date' => $basketDate,
                //         'name' => $basketItem->item->product->name,
                //         'price' => $basketItem->item->product->price,
                //         'quantity' => $quantity,
                //         'gross_income' => $basketItem->total_value,
                //         'income' => $basketItem->total_value + ($basketItem->total_value*.12) ?? 0,
                //     ]);
                // }
            }


            if($basketItem->item->package){

                // get products in package
                // $basketItem->item->package->productsJunction
                //     ->map(function($item) use ($basketItem,$products) {
                //         $quantity = ($basketItem->quantity ?? 1) * ($item->quantity ?? 1); //mutiply by number of products in the package.
                //         $index = $products->search(fn ($test) => $test['product_id'] === $item->product_id);

                //         if ($index !== false) {
                //             $item = $products->get($index);
                //             $item['quantity'] += $quantity;
                //             $products->put($index, $item);
                //         } else {
                //             $products->push([
                //                 'product_id' => $item->product->id,
                //                 'image_path' => $item->product->getMedia()?->first()?->getUrl() ?? asset('images/pos-default.jpg'),
                //                 'name' => $item->product->name,
                //                 'quantity' => $quantity,
                //             ]);
                //         }
                //     });

                // get category income (per package)
                $index = $categories->search(fn ($test) => $test['name'] === 'Packages');

                if ($index !== false) {
                    $item = $categories->get($index);
                    $item['income'] += $basketItem->total_value;
                    $categories->put($index, $item);
                } else {

                    $categories->push([
                        'id' => null,
                        'name' => 'Packages',
                        'income' => $basketItem->total_value ?? 0
                    ]);
                }

                //get all packages
                $index = $packages->search(fn ($test) => $test['id'] === $basketItem->item->package->id);

                $price = ($basketItem->item->package_base_price < 1) ? $basketItem->item->package->base_price : $basketItem->item->package_base_price; // check first if has base price on transaction basket item if not use package base price

                if ($index !== false) {
                    $item = $packages->get($index);
                    $item['quantity'] += $basketItem->quantity;
                    $item['gross_income'] += ($price * $basketItem->quantity);
                    $item['income'] += $basketItem->total_value + ($basketItem->total_value * .12);
                    $packages->put($index, $item);
                } else {
                    $packages->push([
                        'id' => $basketItem->item->package->id,
                        'name' => $basketItem->item->package->name,
                        'quantity' => $basketItem->quantity,
                        'price' =>  $price,
                        'gross_income' => $price * $basketItem->quantity,
                        'income' => $basketItem->total_value + ($basketItem->total_value * .12) ?? 0
                    ]);
                }

                // $index = $detailed_packages->search(fn ($item) => $item['id'] === $basketItem->item->packages->id && $item['user'] === $basketUser && $item['date'] === $basketDate);
                // if ($index !== false){
                //     $item = $detailed_packages->get($index);
                //     $item['quantity'] += $basketItem->quantity;
                //     $item['gross_income'] += $basketItem->total_value;
                //     $detailed_packages->put($index, $item);
                // } else {
                //     $detailed_packages->push([
                //         'id' => $basketItem->item->package->id,
                //         'user' => $basketUser,
                //         'date' => $basketDate,
                //         'name' => $basketItem->item->package->name,
                //         'price' => $basketItem->item->package->base_price,
                //         'quantity' => $basketItem->quantity,
                //         'gross_income' => $basketItem->total_value,
                //         'income' => $basketItem->total_value + ($basketItem->total_value * .12) ?? 0,
                //     ]);
                // }
            }
        }

        $total_per_categories = 0;

        $categories = $categories->map(function($category){
            $category['income'] = $category['income'] + ($category['income'] * .12);
            return $category;
        });

        foreach($categories as $item){
            $total_per_categories += $item['income'];
        }
        $data=[
            'total_transaction' => $transaction->get()->count(),
            'total_sales' => number_format($transaction->get()->sum('total_sales'),2),
            'best_employee' => $bestEmployee,
            'per_categories' => $categories,
            'packages' => $packages,
            'products_excluded_in_package' => $products_excluded_in_package,
            'total_per_categoies' => $total_per_categories,
            'transactions_query' => $transaction,
            'sold_products' => $products->sortByDesc('quantity'),
            'product_trends' => $products->sortByDesc('quantity')->take(8),
            // 'detailed_products' => $detailed_products,
            // 'detailed_packages' => $detailed_packages,
        ];
        return $data;
    }

    public function getEveryfourhours($filter = 'today'){
        $date = Carbon::now();
        if($filter == 'yesterday'){
            $date = Carbon::yesterday();
        }

        $rawData = Transaction::select(
            DB::raw("FLOOR(HOUR(created_at) / 4) AS hour_block"),
            DB::raw("SUM(total_sales) as total_amount")
        )
        ->with('processedBy')
        ->where('is_valid', true)
        ->whereDate('created_at', $date)
        ->groupBy('hour_block')
        ->pluck('total_amount', 'hour_block'); // returns associative array [block => total]

        $results = collect();

        for ($block = 0; $block < 6; $block++) {
            $startHour = $block * 4;
            $endHour = $startHour + 3;

            $label = Carbon::createFromTime($endHour + 1)->format('g A'); // label = end of range + 1
            $timeRange = Carbon::createFromTime($startHour)->format('g A') . ' – ' . Carbon::createFromTime($endHour)->format('g:i A');

            $results->push([
                'label' => $label,
                'time_range' => $timeRange,
                'total_amount' => $rawData[$block] ?? 0,
            ]);
        }

        return $results;

    }
}
