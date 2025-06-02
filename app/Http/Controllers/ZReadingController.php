<?php

namespace App\Http\Controllers;

use App\Models\ReturnTransaction;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\VoidTransaction;
use App\Models\z_record;
use Carbon\Carbon;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Void_;

class ZReadingController extends Controller
{
    public function show()
    {
        $reportDate = Carbon::now()->format('M d, Y');
        $reportTime = Carbon::now()->format('h:i A');

        $shift = Shift::query()
            ->whereBetween('created_at', [
                Carbon::parse($reportDate)->startOfDay(),
                Carbon::parse($reportDate)->endOfDay()
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        $startTime = $shift->first()?->time_in ?? 'N/A';
        $endTime = $shift->last()?->time_out ?? 'N/A';

        $shift = [
            'startTime' => Carbon::parse($startTime)->format('h:i A'),
            'endTime' => Carbon::parse($endTime)->format('h:i A'),
        ];

        $transaction_query = Transaction::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay());

        $transactions = $transaction_query->get();

        $beginningOR = $transactions->first()?->id ?? null;
        $endingOR = $transactions->last()?->id ?? null;

        $void = VoidTransaction::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->get();

        $beginningVoid = $void->first()?->id ?? null;
        $endingVoid = $void->last()?->id ?? null;

        $return = ReturnTransaction::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->get();

        $beginningReturn = $return->first()?->id ?? null;
        $endingReturn = $return->last()?->id ?? null;

        // return response()->json(['message' => 'BREAKPOINT DEBUG']);

        $zRecords = z_record::all();

        if ($zRecords->isNotEmpty()) {
            $resetCounter = $zRecords->last()?->reset_counter;
            $zCounter = $zRecords->last()?->counter + 1;
        }  else {
            $resetCounter = 0;
            $zCounter = 1;
        }

        $z = z_record::create([
            'counter' => $zCounter,
            'reset_counter' => $resetCounter,
        ]);

        if ($z->reset_counter == $resetCounter){
            $salesForTheDay = Transaction::where('is_valid', true)
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->sum('total_sales');
            $previousAccumulated = Transaction::where('is_valid', true)
                ->where('created_at', '<', Carbon::yesterday()->endOfDay())
                ->sum('total_sales');
            $presentAccumulated = $salesForTheDay + $previousAccumulated;

        } else {
            $salesForTheDay = Transaction::where('is_valid', true)
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->sum('total_sales');
            $previousAccumulated = 0;
            $presentAccumulated = Transaction::where('is_valid', true)
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->sum('total_sales');
        }

        $vatableSales = Transaction::where('is_valid', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vatable_sales');

        $vatAmount = Transaction::where('is_valid', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vat');

        $vatExemptSales = Transaction::where('is_valid', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vat_exempt_sales');

        $zeroRatedSales = Transaction::where('is_valid', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('zero_rated_sales');

        $grossAmount = Transaction::where('is_valid', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('gross_sales');

        // For Discounts
        $scDiscounts = 0;
        $pwdDiscounts = 0;
        $nacDiscounts = 0;
        $soloparentDiscounts = 0;
        $totalDiscounts = 0;

        $data_array = [];

        foreach ($transactions as $transaction) {
            $item_array = [];
            foreach ($transaction->basket()->first()->items()->get() as $item) {

                if ($item->discount_value === 0.00) {
                    continue;
                }
                array_push($item_array, $item);
                // return response()->json($item->discount()->exists());

                // return response()->json(['test' => $item]);
                // $discount_id = $item;


                // match ($discount_id) {
                //     1 => $scDiscounts += $item->discount_value,
                //     2 => $pwdDiscounts += $item->discount_value,
                //     3 => $nacDiscounts += $item->discount_value,
                //     4 => $soloparentDiscounts += $item->discount_value,
                // };



                // $totalDiscounts += $item->discount_value;
            }
            array_push($data_array, [$transaction->id => $item_array]);
        }

        return response()->json($data_array);

        return response()->json([
            'reportDate' => $reportDate,
            'reportTime' => $reportTime,
            'startTime' => $shift['startTime'],
            'endTime' => $shift['endTime'],
            'beginningOR' => is_null($beginningOR) ? 'N/A' : str_pad($beginningOR, 6, '0', STR_PAD_LEFT),
            'endingOR' => is_null($endingOR) ? 'N/A' : str_pad($endingOR, 6, '0', STR_PAD_LEFT),
            'beginningVoid' => is_null($beginningVoid) ? 'N/A' : str_pad($beginningVoid, 6, '0', STR_PAD_LEFT),
            'endingVoid' => is_null($endingVoid) ? 'N/A' : str_pad($endingVoid, 6, '0', STR_PAD_LEFT),
            'beginningReturn' => is_null($beginningReturn) ? 'N/A' : str_pad($beginningReturn, 6, '0', STR_PAD_LEFT),
            'endingReturn' => is_null($endingReturn) ? 'N/A' : str_pad($endingReturn, 6, '0', STR_PAD_LEFT),
            'resetCounter' => $resetCounter,
            'zCounter' => str_pad($zCounter, 6, '0', STR_PAD_LEFT),
            'presentAccumulated' => number_format($presentAccumulated, 2, '.', ''),
            'previousAccumulated' => number_format($previousAccumulated, 2, '.', ''),
            'salesForTheDay' => number_format($salesForTheDay, 2, '.', ''),
            'vatableSales' => number_format($vatableSales, 2, '.', ''),
            'vatAmount' => number_format($vatAmount, 2, '.', ''),
            'vatExemptSales' => number_format($vatExemptSales, 2, '.', ''),
            'zeroRatedSales' => number_format($zeroRatedSales, 2, '.', ''),
            'grossAmount' => number_format($grossAmount, 2, '.', ''),
            'scDiscounts' => number_format($scDiscounts, 2, '.', ''),
            'pwdDiscounts' => number_format($pwdDiscounts, 2, '.', ''),
            'nacDiscounts' => number_format($nacDiscounts, 2, '.', ''),
            'soloparentDiscounts' => number_format($soloparentDiscounts, 2, '.', ''),
            'totalDiscounts' => number_format($totalDiscounts, 2, '.', ''),
        ], 200);
    }
}
