<?php

namespace App\Http\Controllers;

use App\Models\ReturnTransaction;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\VoidTransaction;
use App\Models\z_record;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Void_;

class ZReadingController extends Controller
{
    public function show(Request $request)
    {
        $request->validate([
            'currentCash' => 'required|numeric',
        ]);

        $user = Auth::user();

        $reportDate = Carbon::now()->format('M d, Y');
        $reportTime = Carbon::now()->format('h:i A');

        $shift = Shift::query()
            ->whereBetween('created_at', [
                Carbon::parse($reportDate)->startOfDay(),
                Carbon::parse($reportDate)->endOfDay()
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        $startTime = $shift->first()?->time_in;
        $endTime = $shift->last()?->time_out;

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

        $grossAmount = Transaction::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('gross_sales');

        // For Discounts
        $scDiscounts = 0;
        $pwdDiscounts = 0;
        $nacDiscounts = 0;
        $soloparentDiscounts = 0;
        $otherDiscounts = 0;
        $totalDiscounts = 0;


        foreach ($transactions as $transaction) {
            foreach ($transaction->basket()->first()->items()->get() as $item) {

                if ($item->discount_value === 0.00) {
                    continue;
                }

                $totalDiscounts += $item->discount_value;

                $discount_id = $item->discounts()->first()?->discount_id;

                match ($discount_id) {
                    1 => $scDiscounts += $item->discount_value,
                    2 => $pwdDiscounts += $item->discount_value,
                    3 => $nacDiscounts += $item->discount_value,
                    4 => $soloparentDiscounts += $item->discount_value,
                    default => $otherDiscounts += $item->discount_value,
                };
            }
        }

        // less discount, less return, less void, less vat adjust, net amount

        // FOR LESS CALCULATIONS

        $totalReturns = 0;
        $totalVoids = Transaction::where('is_valid', false)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('total_sales');
        $totalVATAdjusts = Transaction::where('is_valid', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vat_adjustment');

        $lessDiscounts = $grossAmount - $totalDiscounts;
        $lessReturns = $lessDiscounts - $totalReturns;
        $lessVoids = $lessDiscounts - $totalVoids;
        $lessVATAdjustments = $lessVoids - $totalVATAdjusts;
        $netAmount = $lessVATAdjustments;

        $scTransactionsVATAdjust = Transaction::where('is_sc', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->where('is_valid', true)
            ->sum('vat_adjustment');

        $pwdTransactionsVATAdjust = Transaction::where('is_pwd', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->where('is_valid', true)
            ->sum('vat_adjustment');

        $regDiscountsVATAdjust = Transaction::where('is_valid', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->where('is_sc', false)
            ->where('is_pwd', false)
            ->orWhere('is_nac', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->orWhere('is_soloparent', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vat_adjustment');

        $zeroRatedVATAdjust = 0;
        $returnVATAdjust = 0;

        $otherVATAdjust = Transaction::where('is_valid', false)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vat_adjustment');

        // TRANSACTION SUMMARY

        $totalChange = $transactions->where('transaction_method_id', 1)->where('is_valid', true)->sum('change');

        $totalCashPayment = $transactions->where('transaction_method_id', 1)->where('is_valid', true)->sum('total_sales');
        $totalGcashPayment = $transactions->where('transaction_method_id', 2)->where('is_valid', true)->sum('total_sales');
        $totalMayaPayment = $transactions->where('transaction_method_id', 3)->where('is_valid', true)->sum('total_sales');
        $totalDebitPayment = $transactions->where('transaction_method_id', 4)->where('is_valid', true)->sum('total_sales');
        $totalCreditPayment = $transactions->where('transaction_method_id', 5)->where('is_valid', true)->sum('total_sales');


        $totalDigitalPayment = $transactions
            ->whereNotIn('transaction_method_id', [1, 5])
            ->where('is_valid', true)
            ->sum('total_sales');

        $totalPayments = $transactions->sum('total_sales');

        $openingBalance = Shift::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->first()?->opening_balance ?? 0;

        $cashInDrawer = $openingBalance + $totalCashPayment;

        $withdrawal = $totalChange;
        $lessWithdrawal = $cashInDrawer - $totalChange;

        $shortOrOver = $request->currentCash - $cashInDrawer;

        $reportDate = Carbon::now()->format('M d, Y');
        $reportTime = Carbon::now()->format('h:i A');

        $z->update([
            'report_date' => $reportDate ?? 'N/A',
            'report_time' => $reportTime ?? 'N/A',
            'start_time' => $shift['startTime'] ?? 'N/A',
            'end_time' => $shift['endTime'] ?? 'N/A',
            'beginning_si' => is_null($beginningOR) ? 'N/A' : str_pad($beginningOR, 12, '0', STR_PAD_LEFT),
            'ending_si' => is_null($endingOR) ? 'N/A' : str_pad($endingOR, 12, '0', STR_PAD_LEFT),
            'beginning_void' => is_null($beginningVoid) ? 'N/A' : str_pad($beginningVoid, 12, '0', STR_PAD_LEFT),
            'ending_void' => is_null($endingVoid) ? 'N/A' : str_pad($endingVoid, 12, '0', STR_PAD_LEFT),
            'reset_counter' => $resetCounter,
            'counter' => $zCounter,
            'present_accumulated_sales' => $presentAccumulated,
            'previous_accumulated_sales' => $previousAccumulated,
            'sales_for_the_day' => $salesForTheDay,
            'vatable_sales' => $vatableSales,
            'vat' => $vatAmount,
            'vat_exempt_sales' => $vatExemptSales,
            'zero_rated_sales' => $zeroRatedSales,
            'gross_amount' => $grossAmount,
            'less_discount' => $lessDiscounts,
            'less_void' => $lessVoids,
            'less_vat_adjust' => $lessVATAdjustments,
            'net_amount' => $netAmount,
            'sc_discounts' => $scDiscounts,
            'pwd_discounts' => $pwdDiscounts,
            'naac_discounts' => $nacDiscounts,
            'sp_discounts' => $soloparentDiscounts,
            'other_discounts' => $otherDiscounts,
            'void' => $totalVoids,
            'returns' => $totalReturns,
            'sc_adjustments' => $scTransactionsVATAdjust,
            'pwd_adjustments' => $pwdTransactionsVATAdjust,
            'reg_discount_adjustments' => $regDiscountsVATAdjust,
            'zero_rated_adjustments' => $zeroRatedVATAdjust,
            'vat_on_return' => $returnVATAdjust,
            'other_vat_adjustments' => $otherVATAdjust,
            'cash_in_drawer' => $cashInDrawer,
            'gcash_payments' => $totalGcashPayment,
            'maya_payments' => $totalMayaPayment,
            'debit_payments' => $totalDebitPayment,
            'credit_payments' => $totalCreditPayment,
            'opening_fund' => $openingBalance,
            'withdrawal' => $withdrawal,
            'less_withdrawal' => $lessWithdrawal,
            'payments_received' => $totalPayments,
            'short_over' => $shortOrOver,
        ]);

        return response()->json([
            'reportDate' => $reportDate,
            'reportTime' => $reportTime,
            'startTime' => $shift['startTime'],
            'endTime' => $shift['endTime'],
            'beginningOR' => is_null($beginningOR) ? 'N/A' : str_pad($beginningOR, 12, '0', STR_PAD_LEFT),
            'endingOR' => is_null($endingOR) ? 'N/A' : str_pad($endingOR, 12, '0', STR_PAD_LEFT),
            'beginningVoid' => is_null($beginningVoid) ? 'N/A' : str_pad($beginningVoid, 12, '0', STR_PAD_LEFT),
            'endingVoid' => is_null($endingVoid) ? 'N/A' : str_pad($endingVoid, 12, '0', STR_PAD_LEFT),
            'beginningReturn' => is_null($beginningReturn) ? 'N/A' : str_pad($beginningReturn, 12, '0', STR_PAD_LEFT),
            'endingReturn' => is_null($endingReturn) ? 'N/A' : str_pad($endingReturn, 12, '0', STR_PAD_LEFT),
            'resetCounter' => $resetCounter,
            'zCounter' => str_pad($zCounter, 12, '0', STR_PAD_LEFT),
            'presentAccumulated' => number_format($presentAccumulated, 2, '.', ''),
            'previousAccumulated' => number_format($previousAccumulated, 2, '.', ''),
            'salesForTheDay' => number_format($salesForTheDay, 2, '.', ''),
            'vatableSales' => number_format($vatableSales, 2, '.', ''),
            'vatAmount' => number_format($vatAmount, 2, '.', ''),
            'vatExemptSales' => number_format($vatExemptSales, 2, '.', ''),
            'zeroRatedSales' => number_format($zeroRatedSales, 2, '.', ''),
            'grossAmount' => number_format($grossAmount, 2, '.', ''),
            'lessDiscounts' => number_format($lessDiscounts, 2, '.', ''),
            'lessReturns' => number_format($lessReturns, 2, '.', ''),
            'lessVoids' => number_format($lessVoids, 2, '.', ''),
            'lessVATAdjustments' => number_format($lessVATAdjustments, 2, '.', ''),
            'netAmount' => number_format($netAmount, 2, '.', ''),
            'scDiscounts' => number_format($scDiscounts, 2, '.', ''),
            'pwdDiscounts' => number_format($pwdDiscounts, 2, '.', ''),
            'nacDiscounts' => number_format($nacDiscounts, 2, '.', ''),
            'soloparentDiscounts' => number_format($soloparentDiscounts, 2, '.', ''),
            'otherDiscounts' => number_format($otherDiscounts, 2, '.', ''),
            'totalVoids' => number_format($totalVoids, 2, '.', ''),
            'totalReturns' => number_format($totalReturns, 2, '.', ''),
            'scTransactionsVATAdjust' => number_format($scTransactionsVATAdjust, 2, '.', ''),
            'pwdTransactionsVATAdjust' => number_format($pwdTransactionsVATAdjust, 2, '.', ''),
            'regDiscountsVATAdjust' => number_format($regDiscountsVATAdjust, 2, '.', ''),
            'zeroRatedVATAdjust' => number_format($zeroRatedVATAdjust, 2, '.', ''),
            'returnVATAdjust' => number_format($returnVATAdjust, 2, '.', ''),
            'otherVATAdjust' => number_format($otherVATAdjust, 2, '.', ''),
            'cashInDrawer' => number_format($cashInDrawer, 2, '.', ''),
            'digitalPayments' => number_format($totalDigitalPayment, 2, '.', ''),
            'cashPayments' => number_format($totalCashPayment, 2, '.', ''),
            'gcashPayments' => number_format($totalGcashPayment, 2, '.', ''),
            'mayaPayments' => number_format($totalMayaPayment, 2, '.', ''),
            'debitPayments' => number_format($totalDebitPayment, 2, '.', ''),
            'creditPayments' => number_format($totalCreditPayment, 2, '.', ''),
            'openingBalance' => number_format($openingBalance, 2, '.', ''),
            'withdrawal' => number_format($withdrawal, 2, '.', ''),
            'lessWithdrawal' => number_format($lessWithdrawal, 2, '.', ''),
            'totalPayments' => number_format($totalPayments, 2, '.', ''),
            'shortOrOver' => number_format($shortOrOver, 2, '.', ''),
        ], 200);
    }
}
