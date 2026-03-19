<?php

namespace App\Http\Controllers;

use App\Journal\Journal;
use App\Models\ReturnTransaction;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\VoidTransaction;
use App\Models\z_record;
use Carbon\Carbon;
use Exception;
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
            'startTime' => Carbon::parse($startTime)->format('M d, Y - h:i A'),
            'endTime' => Carbon::parse($endTime)->format('M d, Y - h:i A'),
        ];

        $transaction_query = Transaction::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay());

        $transactions = $transaction_query->get();

        $beginningOR = $transactions->first()?->si_no ?? null;
        $endingOR = $transactions->last()?->si_no ?? null;

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
            $salesForTheDay = Transaction::query()
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->sum('gross_sales');
            $previousAccumulated = Transaction::query()
                ->where('created_at', '<', Carbon::yesterday()->endOfDay())
                ->sum('gross_sales');
            $presentAccumulated = $salesForTheDay + $previousAccumulated;

        } else {
            $salesForTheDay = Transaction::query()
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->sum('gross_sales');
            $previousAccumulated = 0;
            $presentAccumulated = Transaction::query()
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->sum('gross_sales');
        }

        $vatableSales = Transaction::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vatable_sales');

        $vatAmount = Transaction::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vat');

        $vatExemptSales = Transaction::where('created_at', '>=', Carbon::now()->startOfDay())
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
        $totalVoidsVatable = Transaction::where('is_valid', false)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vatable_sales');
        $totalVoidsVatExempt = Transaction::where('is_valid', false)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vat_exempt_sales');
        $totalVoids = $totalVoidsVatable + $totalVoidsVatExempt;

        $scTransactionsVATAdjust = Transaction::where('is_sc', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->where('is_valid', false)
            ->sum('vat_adjustment');

        $pwdTransactionsVATAdjust = Transaction::where('is_pwd', true)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->where('is_valid', false)
            ->sum('vat_adjustment');

        $todayStart = Carbon::now()->startOfDay();
        $todayEnd   = Carbon::now()->endOfDay();

        $regDiscountsVATAdjust = Transaction::where('is_valid', false)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->where(function ($q) {
                $q->where('is_nac', true)
                ->orWhere('is_soloparent', true)
                ->orWhere(function ($q2) {
                    $q2->where('is_sc', false)
                    ->where('is_pwd', false);
                });
            })
            ->sum('vat_adjustment');

        $zeroRatedVATAdjust = 0;
        $otherVATAdjust = 0;

        $returnVATAdjust = Transaction::where('is_valid', false)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->sum('vatable_sales') * 0.12;

        $totalVATAdjusts = $scTransactionsVATAdjust + $pwdTransactionsVATAdjust + $regDiscountsVATAdjust + $zeroRatedVATAdjust + $otherVATAdjust + $returnVATAdjust;


        $lessDiscounts = $grossAmount - $totalDiscounts;
        $lessReturns = $lessDiscounts - $totalReturns;
        $lessVoids = $lessDiscounts - $totalVoids;
        $lessVATAdjustments = $lessVoids - $totalVATAdjusts;
        $netAmount = $grossAmount - ($totalDiscounts + $totalVoids + $totalVATAdjusts + $totalReturns + $vatAmount);

        // TRANSACTION SUMMARY

        $totalChange = $transaction_query->where('is_valid', 1)->
        with(['paymentMethods' => function ($query) {
            $query->where('payment_method_id', 1);
        }])
        ->get()
        ->sum('change');

        $totalCashPayment = $transaction_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 1);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalGcashPayment = $transaction_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 2);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalMayaPayment = $transaction_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 3);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalDebitPayment = $transaction_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 4);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalCreditPayment = $transaction_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 5);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalDigitalPayment = $transaction_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->whereNotIn('payment_method_id', [1,2,3,4,5]);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalPayments = $transaction_query->where('is_valid', true)
        ->withSum('paymentMethods','cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $openingBalance = Shift::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->first()?->opening_balance ?? 0;

        $cashInDrawer = $openingBalance + $totalCashPayment;

        $withdrawal = $totalChange;
        $lessWithdrawal = $cashInDrawer - $totalChange;

        $shortOrOver = $request->currentCash - $lessWithdrawal;

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
            'reset_counter' => str_pad($resetCounter, 12, '0', STR_PAD_LEFT),
            'counter' => str_pad($zCounter, 12, '0', STR_PAD_LEFT),
            'present_accumulated_sales' => $presentAccumulated,
            'previous_accumulated_sales' => $previousAccumulated,
            'sales_for_the_day' => $salesForTheDay,
            'vatable_sales' => $vatableSales,
            'vat' => $vatAmount,
            'vat_exempt_sales' => $vatExemptSales,
            'zero_rated_sales' => $zeroRatedSales,
            'gross_amount' => $grossAmount,
            'total_discounts' => $totalDiscounts,
            'total_vat_adjusts' => $totalVATAdjusts,
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


        //Create Journal List
        $_journal_z_reading = [
            '   THERMOZONE PHILIPPINES CORP.   ',
            '      2286 Marconi St., Brgy.     ',
            '     San Isidro City of Makati,   ',
            '  VAT REG TIN: 223-661-818-00000  ',
            '         MIN: '.'XXXXXXXXXX       ',
            '         S/N: '.'XXXXXXXXXX       ',
            ' ',
            '          Z READING REPORT       ',
            ' Report Date: '.str_pad($reportDate, 19, ' ', STR_PAD_LEFT),
            ' Report Time: '.str_pad($reportTime, 19, ' ', STR_PAD_LEFT),
            ' ',
            ' Start Date & Time: ',
            str_pad($shift['startTime'], 20, ' ', STR_PAD_LEFT),
            '',
            ' End Date & Time: ',
            str_pad($shift['endTime'], 22, ' ', STR_PAD_LEFT),
            ' ',
            ' Beg. SI #: '.str_pad(is_null($beginningOR) ? 'N/A' : str_pad($beginningOR, 12, '0', STR_PAD_LEFT), 21, ' ', STR_PAD_LEFT),
            ' End SI #: '.str_pad(is_null($endingOR) ? 'N/A' : str_pad($endingOR, 12, '0', STR_PAD_LEFT), 22, ' ', STR_PAD_LEFT),
            ' Beg. Void #:'.str_pad(is_null($beginningVoid) ? 'N/A' : str_pad($beginningVoid, 12, '0', STR_PAD_LEFT), 20, ' ', STR_PAD_LEFT),
            ' End Void #:'.str_pad(is_null($endingVoid) ? 'N/A' : str_pad($endingVoid, 12, '0', STR_PAD_LEFT), 21, ' ', STR_PAD_LEFT),
            ' ',
            ' Reset Counter N'.str_pad(str_pad($resetCounter, 12, '0', STR_PAD_LEFT), 17, ' ', STR_PAD_LEFT),
            ' Z Counter No:'.str_pad(str_pad($zCounter, 12, '0', STR_PAD_LEFT), 19, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            ' Present Accumul'.str_pad(number_format($presentAccumulated, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Previous Accumu'.str_pad(number_format($previousAccumulated, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Sales for the D'.str_pad(number_format($salesForTheDay, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '        BREAKDOWN OF SALES        ',
            ' VATABLE SALES: '.str_pad(number_format($vatableSales, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' VAT AMOUNT: '.str_pad(number_format($vatAmount, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' VAT EXEMPT SALE'.str_pad(number_format($vatExemptSales, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' ZERO RATED SALE'.str_pad(number_format($zeroRatedSales, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            ' Gross Amount: '.str_pad(number_format($grossAmount, 2, '.', ''), 18, ' ', STR_PAD_LEFT),
            ' Total Discounts: '.str_pad(number_format($totalDiscounts, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Total Void: ' . str_pad(number_format($totalVoids, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Total VAT Adjus'.str_pad(number_format($totalVATAdjusts, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Net Amount: '.str_pad(number_format($netAmount, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '         DISCOUNT SUMMARY         ',
            ' SC Disc. :'.str_pad(number_format($scDiscounts, 2, '.', ''), 22, ' ', STR_PAD_LEFT),
            ' PWD Disc. :'.str_pad(number_format($pwdDiscounts, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' NAAC Disc. :'.str_pad(number_format($nacDiscounts, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Solo Parent Dis'.str_pad(number_format($soloparentDiscounts, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Other Disc. :'.str_pad(number_format($otherDiscounts, 2, '.', ''), 19, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '         SALES ADJUSTMENT         ',
            ' VOID :'.str_pad(number_format($totalVoids, 2, '.', ''), 26, ' ', STR_PAD_LEFT),
            ' RETURN :'.str_pad(number_format($totalReturns, 2, '.', ''), 24, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '          VAT ADJUSTMENT          ',
            ' SC TRANS. :'.str_pad(number_format($scTransactionsVATAdjust, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' PWD TRANS. :'.str_pad(number_format($pwdTransactionsVATAdjust, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Reg.Disc. TRANS'.str_pad(number_format($regDiscountsVATAdjust, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' ZERO-RATED TRAN'.str_pad(number_format($zeroRatedVATAdjust, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' VAT on Return :'.str_pad(number_format($returnVATAdjust, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Other VAT Adjus'.str_pad(number_format($otherVATAdjust, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '        TRANSACTION SUMMARY       ',
            ' CASH IN DRAWER'.str_pad(number_format($cashInDrawer, 2, '.', ''), 18, ' ', STR_PAD_LEFT),
            ' GCASH PAYMENTS'.str_pad(number_format($totalGcashPayment, 2, '.', ''), 18, ' ', STR_PAD_LEFT),
            ' MAYA PAYMENTS'.str_pad(number_format($totalMayaPayment, 2, '.', ''), 19, ' ', STR_PAD_LEFT),
            ' DEBIT CARD'.str_pad(number_format($totalDebitPayment, 2, '.', ''), 22, ' ', STR_PAD_LEFT),
            ' CREDIT CARD'.str_pad(number_format($totalCreditPayment, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' Opening Fund'.str_pad(number_format($openingBalance, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' WITHDRAWAL :'.str_pad(number_format($withdrawal, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Less Withdrawal'.str_pad(number_format($lessWithdrawal, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Payments Receiv'.str_pad(number_format($totalPayments, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            ' SHORT/OVER :'.str_pad(number_format($shortOrOver, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' -------------------------------- '.PHP_EOL,
            '---------------------------------------------------------------------------' . PHP_EOL,
        ];

        Journal::createJournalEntry(Carbon::now()->format('Ymd'), 'z_reading',  $_journal_z_reading);

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
            'resetCounter' => str_pad($resetCounter, 12, '0', STR_PAD_LEFT),
            'zCounter' => str_pad($zCounter, 12, '0', STR_PAD_LEFT),
            'presentAccumulated' => number_format($presentAccumulated, 2, '.', ''),
            'previousAccumulated' => number_format($previousAccumulated, 2, '.', ''),
            'salesForTheDay' => number_format($salesForTheDay, 2, '.', ''),
            'vatableSales' => number_format($vatableSales, 2, '.', ''),
            'vatAmount' => number_format($vatAmount, 2, '.', ''),
            'vatExemptSales' => number_format($vatExemptSales, 2, '.', ''),
            'zeroRatedSales' => number_format($zeroRatedSales, 2, '.', ''),
            'grossAmount' => number_format($grossAmount, 2, '.', ''),
            'totalDiscounts' => number_format($totalDiscounts, 2, '.', ''),
            'totalVATAdjusts' => number_format($totalVATAdjusts, 2, '.', ''),
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

    public function reprint(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);

        $latestZ = z_record::whereDate('created_at', $date->toDateString())
            ->latest()
            ->first();


        //Create Journal List
        $_journal_z_reading = [
            '  -----       REPRINT       ----  ',
            'Date :' . str_pad(now()->format('F d, Y'), 28, ' ', STR_PAD_LEFT),
            'Time :' . str_pad(now()->format('h:i A'), 28, ' ', STR_PAD_LEFT) . PHP_EOL,
            '   THERMOZONE PHILIPPINES CORP.   ',
            '      2286 Marconi St., Brgy.     ',
            '     San Isidro City of Makati,   ',
            '  VAT REG TIN: 223-661-818-00000  ',
            '         MIN: '.'XXXXXXXXXX       ',
            '         S/N: '.'XXXXXXXXXX       ',
            ' ',
            '          Z READING REPORT       ',
            ' Report Date: '.str_pad($latestZ->report_date, 19, ' ', STR_PAD_LEFT),
            ' Report Time: '.str_pad($latestZ->report_time, 19, ' ', STR_PAD_LEFT),
            ' ',
            ' Start Date & Time: ',
            str_pad($latestZ->start_time, 20, ' ', STR_PAD_LEFT),
            '',
            ' End Date & Time: ',
            str_pad($latestZ->end_time, 22, ' ', STR_PAD_LEFT),
            ' ',
            ' Beg. SI #: '.str_pad(is_null($latestZ->beginning_si) ? 'N/A' : str_pad($latestZ->beginning_si, 12, '0', STR_PAD_LEFT), 21, ' ', STR_PAD_LEFT),
            ' End SI #: '.str_pad(is_null($latestZ->ending_si) ? 'N/A' : str_pad($latestZ->ending_si, 12, '0', STR_PAD_LEFT), 22, ' ', STR_PAD_LEFT),
            ' Beg. Void #:'.str_pad(is_null($latestZ->beginning_void) ? 'N/A' : str_pad($latestZ->beginning_void, 12, '0', STR_PAD_LEFT), 20, ' ', STR_PAD_LEFT),
            ' End Void #:'.str_pad(is_null($latestZ->ending_void) ? 'N/A' : str_pad($latestZ->ending_void, 12, '0', STR_PAD_LEFT), 21, ' ', STR_PAD_LEFT),
            ' ',
            ' Reset Counter N'.str_pad(str_pad($latestZ->reset_counter, 12, '0', STR_PAD_LEFT), 17, ' ', STR_PAD_LEFT),
            ' Z Counter No:'.str_pad(str_pad($latestZ->counter, 12, '0', STR_PAD_LEFT), 19, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            ' Present Accumul'.str_pad(number_format($latestZ->present_accumulated_sales, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Previous Accumu'.str_pad(number_format($latestZ->previous_accumulated_sales, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Sales for the D'.str_pad(number_format($latestZ->sales_for_the_day, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '        BREAKDOWN OF SALES        ',
            ' VATABLE SALES: '.str_pad(number_format($latestZ->vatable_sales, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' VAT AMOUNT: '.str_pad(number_format($latestZ->vat, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' VAT EXEMPT SALE'.str_pad(number_format($latestZ->vat_exempt_sales, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' ZERO RATED SALE'.str_pad(number_format($latestZ->zero_rated_sale, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            ' Gross Amount: '.str_pad(number_format($latestZ->gross_amount, 2, '.', ''), 18, ' ', STR_PAD_LEFT),
            ' Total Discounts: '.str_pad(number_format($latestZ->total_discounts, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Total Void: '.str_pad(number_format($latestZ->void, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Total VAT Adjus'.str_pad(number_format($latestZ->total_vat_adjusts, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Net Amount: '.str_pad(number_format($latestZ->net_amount, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '         DISCOUNT SUMMARY         ',
            ' SC Disc. :'.str_pad(number_format($latestZ->sc_discounts, 2, '.', ''), 22, ' ', STR_PAD_LEFT),
            ' PWD Disc. :'.str_pad(number_format($latestZ->pwd_discounts, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' NAAC Disc. :'.str_pad(number_format($latestZ->naac_discounts, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Solo Parent Dis'.str_pad(number_format($latestZ->sp_discounts, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Other Disc. :'.str_pad(number_format($latestZ->other_discounts, 2, '.', ''), 19, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '         SALES ADJUSTMENT         ',
            ' VOID :'.str_pad(number_format($latestZ->void, 2, '.', ''), 26, ' ', STR_PAD_LEFT),
            ' RETURN :'.str_pad(number_format($latestZ->returns, 2, '.', ''), 24, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '          VAT ADJUSTMENT          ',
            ' SC TRANS. :'.str_pad(number_format($latestZ->sc_adjustments, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' PWD TRANS. :'.str_pad(number_format($latestZ->pwd_adjustments, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Reg.Disc. TRANS'.str_pad(number_format($latestZ->reg_discount_adjustments, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' ZERO-RATED TRAN'.str_pad(number_format($latestZ->zero_rated_adjustments, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' VAT on Return :'.str_pad(number_format($latestZ->vat_on_return, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Other VAT Adjus'.str_pad(number_format($latestZ->other_vet_adjustments, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            '        TRANSACTION SUMMARY       ',
            ' CASH IN DRAWER'.str_pad(number_format($latestZ->cash_in_drawer, 2, '.', ''), 18, ' ', STR_PAD_LEFT),
            ' GCASH PAYMENTS'.str_pad(number_format($latestZ->gcash_payments, 2, '.', ''), 18, ' ', STR_PAD_LEFT),
            ' MAYA PAYMENTS'.str_pad(number_format($latestZ->maya_payments, 2, '.', ''), 19, ' ', STR_PAD_LEFT),
            ' DEBIT CARD'.str_pad(number_format($latestZ->debit_payments, 2, '.', ''), 22, ' ', STR_PAD_LEFT),
            ' CREDIT CARD'.str_pad(number_format($latestZ->credit_payments, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' Opening Fund'.str_pad(number_format($latestZ->opening_fund, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' WITHDRAWAL :'.str_pad(number_format($latestZ->withdrawal, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Less Withdrawal'.str_pad(number_format($latestZ->less_withdrawal, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Payments Receiv'.str_pad(number_format($latestZ->payments_recieved, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' -------------------------------- ',
            ' SHORT/OVER :'.str_pad(number_format($latestZ->short_over, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' -------------------------------- '.PHP_EOL,
            '---------------------------------------------------------------------------' . PHP_EOL,
        ];

        Journal::createJournalEntry(Carbon::now()->format('Ymd'), 'z_reading_reprint',  $_journal_z_reading);

        if (!$latestZ) {
            return response()->json([
                'message' => "No Z Reading record found for {$date->toDateString()}."
            ], 404);
        }

        return response()->json($latestZ, 200);
    }

    public function zReading_summary(Request $request)
    {

        try {
            $request->validate([
                'date_from' => 'required|date',
                'date_to' => 'required|date',
            ]);

            $start_date = Carbon::parse($request->date_from)->startOfDay();
            $end_date = Carbon::parse($request->date_to)->endOfDay();

            $startDate = Carbon::parse($request->date_from)->format('F d, Y');
            $endDate = Carbon::parse($request->date_to)->format('F d, Y');

            $z_Readings = z_record::whereBetween('created_at', [$start_date, $end_date]);

            if (empty($z_Readings->get())) {
                return response()->json([
                    'message' => "No Z Reading record found on date {$start_date->toDateString()} - {$end_date->toDateString()}."
                ], 404);
            }

            $first = $z_Readings->orderBy('created_at','asc')->first();
            $last = $z_Readings->orderBy('created_at','desc')->first();

            $beg_void = $z_Readings->where('beginning_void','!=','N/A')->orderBy('created_at','asc')->first();
            $ending_void = $z_Readings->where('ending_void','!=','N/A')->orderBy('created_at','desc')->first();

            $data = [];
            $exclude = ['beginning_si', 'start_time', 'updated_at', 'created_at','end_time','ending_si','beginning_void','ending_void','id','counter','reset_counter'];

            foreach ($z_Readings->first()->getAttributes() as $column => $value) {
                if (!in_array($column, $exclude) && is_numeric($value)) {
                    $data[$column] = number_format($z_Readings->sum($column), 2);
                }
            }

            $data['beginningOR'] =  $first->beginning_si ? sprintf('%012d', $first->beginning_si) : 'N/A';
            $data['endingOR'] = $last->ending_si ? sprintf('%012d', $last->ending_si) : 'N/A';
            $data['beginningVoid'] = $beg_void->beginning_void ? sprintf('%012d', $beg_void->beginning_void) : 'N/A';
            $data['endingVoid'] = $ending_void->ending_void ? sprintf('%012d', $ending_void->ending_void) : 'N/A';
            $data['report_date'] = Carbon::now()->format('M d, Y');
            $data['report_time'] = Carbon::now()->format('h:i A');
            $data['total_invoices'] = $z_Readings->count();
            $data['start_date'] = $startDate;
            $data['end_date'] = $endDate;



            return response()->json($data, 200);

        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);

        }


    }

}
