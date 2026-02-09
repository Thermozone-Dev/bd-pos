<?php

namespace App\Http\Controllers\Api\v1;

use App\Filament\Loggers\ShiftLogger;
use App\Filament\Loggers\TransactionLogger;
use App\Http\Controllers\Controller;
use App\Journal\Journal;
use App\Models\Transaction;
use App\Models\User;
use App\Models\x_record;
use App\Models\XReading;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class XReadingController extends Controller
{

    public function show(Request $request)
    {
        $request->validate([
            'currentCash' => 'required|numeric',
        ]);

        $user = Auth::user();
        $shift = $user->shifts()->latest()->first();

        ShiftLogger::make($shift)->requestXReading();

        if (!$shift) {
            return response()->json(['message' => 'No shift found.'], 404);
        }

        $reportDate = Carbon::now()->format('M d, Y');
        $reportTime = Carbon::now()->format('h:i A');

        $inTime = $shift->time_in;

        $time_in = Carbon::parse($shift->time_in)->format('M d, Y - h:i A');
        $time_out = Carbon::now()->format('M d, Y - h:i A');

        $transactions_query = Transaction::where('created_at', '>=', $inTime)->where('processed_by', $user->id);
        $transactions = $transactions_query->get();

        $startOR = $transactions->first()?->si_no ?? null;
        $beginningOR = str_pad($startOR, 12, '0', STR_PAD_LEFT);
        $endOR = $transactions->last()?->si_no ?? null;
        $endingOR = str_pad($endOR, 12, '0', STR_PAD_LEFT);

        $openingFund = $shift->opening_balance;
        $endingFund = $shift->ending_balance;

        // $totalChange = $transactions->where('transaction_method_id', 1)->where('is_valid', true)->sum('change');
        $totalChange = $transactions_query->where('is_valid', 1)->
        with(['paymentMethods' => function ($query) {
            $query->where('payment_method_id', 1);
        }])
        ->get()
        ->sum('change');

        // $totalCashPayment = $transactions->where('transaction_method_id', 1)->where('is_valid', true)->sum('total_sales');
        $totalCashPayment = $transactions_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 1);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalGcashPayment = $transactions_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 2);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalMayaPayment = $transactions_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 3);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalDebitPayment = $transactions_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 4);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalCreditPayment = $transactions_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->where('payment_method_id', 5);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalDigitalPayment = $transactions_query->where('is_valid', true)
        ->withSum(['paymentMethods' => function($query) {
            $query->whereNotIn('payment_method_id', [1,2,3,4,5]);
        }],'cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $totalPayments = $transactions_query->where('is_valid', true)
        ->withSum('paymentMethods','cash_tendered')
        ->get()
        ->sum('payment_methods_sum_cash_tendered');

        $voidValue = $transactions
            ->where('is_valid', false)
            ->sum('total_sales');

        $refundValue = 0;

        $cashInDrawer = $openingFund + $totalCashPayment;

        $withdrawal = $totalChange;
        $lessWithdrawal = $cashInDrawer - $totalChange;

        $shortOrOver = $request->currentCash - $lessWithdrawal;

        x_record::create([
            'generated_by' => $user->id,
            'report_date' => $reportDate,
            'report_time' => $reportTime,
            'start_time' => $time_in,
            'end_time' => $time_out,
            'cashier_name' => $user->name,
            'beginning_si' => $beginningOR,
            'ending_si' => $endingOR,
            'opening_fund' => $openingFund,
            'cash_payments' => $totalCashPayment,
            'gcash_payments' => $totalGcashPayment,
            'maya_payments' => $totalMayaPayment,
            'debit_payments' => $totalDebitPayment,
            'credit_payments' => $totalCreditPayment,
            'total_payments' => $totalPayments,
            'void' => $voidValue,
            'withdrawal' => $withdrawal,
            'cash_in_drawer' => $cashInDrawer,
            'less_withdrawal' => $lessWithdrawal,
            'short_over' => $shortOrOver,
        ]);

        //Create Journal List
        $_journal_x_reading = [
            '   THERMOZONE PHILIPPINES CORP.   ',
            ' 2286 Marconi St., Brgy. San Isid ',
            '        ro City of Makati,        ',
            '  VAT REG TIN: 223-661-818-00000  ',
            '         MIN: '.'XXXXXXXXXX       ',
            '         S/N: '.'XXXXXXXXXX       ',
            ' ',
            '          X READING REPORT       ',
            ' ',
            ' Report Date: '.str_pad($reportDate, 19, ' ', STR_PAD_LEFT),
            ' Report Time: '.str_pad($reportTime, 19, ' ', STR_PAD_LEFT),
            '',
            ' Start Time: '.str_pad($shift['startTime'], 20, ' ', STR_PAD_LEFT),
            ' End Time: '.str_pad($shift['endTime'], 22, ' ', STR_PAD_LEFT),
            ' ',
            ' Cashier: '.str_pad($user->name, 23, ' ', STR_PAD_LEFT),
            ' ',
            ' Beg. SI #: '.str_pad(is_null($beginningOR) ? 'N/A' : str_pad($beginningOR, 12, '0', STR_PAD_LEFT), 21, ' ', STR_PAD_LEFT),
            ' End SI #: '.str_pad(is_null($endingOR) ? 'N/A' : str_pad($endingOR, 12, '0', STR_PAD_LEFT), 22, ' ', STR_PAD_LEFT),
            ' ',
            ' Opening Fund'.str_pad(number_format($openingFund, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' ================================ ',
            'PAYMENTS RECIEVED                 ',
            ' CASH'.str_pad(number_format($cashInDrawer, 2, '.', ''), 28, ' ', STR_PAD_LEFT),
            ' GCASH '.str_pad(number_format($totalGcashPayment, 2, '.', ''), 26, ' ', STR_PAD_LEFT),
            ' MAYA '.str_pad(number_format($totalMayaPayment, 2, '.', ''), 27, ' ', STR_PAD_LEFT),
            ' DEBIT CARD'.str_pad(number_format($totalDebitPayment, 2, '.', ''), 22, ' ', STR_PAD_LEFT),
            ' CREDIT CARD'.str_pad(number_format($totalCreditPayment, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' Total Payments:'.str_pad(number_format($totalPayments, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' ================================ ',
            ' VOID: '.str_pad(number_format($voidValue, 2, '.', ''), 26, ' ', STR_PAD_LEFT),
            ' ================================ ',
            ' WITHDRAWAL: '.str_pad(number_format($withdrawal, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' ================================ ',
            'TRANSACTION SUMMARY               ',
            ' CASH IN DRAWER'.str_pad(number_format($cashInDrawer, 2, '.', ''), 18, ' ', STR_PAD_LEFT),
            ' GCASH '.str_pad(number_format($totalGcashPayment, 2, '.', ''), 26, ' ', STR_PAD_LEFT),
            ' MAYA '.str_pad(number_format($totalMayaPayment, 2, '.', ''), 27, ' ', STR_PAD_LEFT),
            ' DEBIT CARD'.str_pad(number_format($totalDebitPayment, 2, '.', ''), 22, ' ', STR_PAD_LEFT),
            ' CREDIT CARD'.str_pad(number_format($totalCreditPayment, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' Opening Fund'.str_pad(number_format($openingFund, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' WITHDRAWAL :'.str_pad(number_format($withdrawal, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Less Withdrawal'.str_pad(number_format($lessWithdrawal, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Payments Receiv'.str_pad(number_format($totalPayments, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' ================================ ',
            ' SHORT/OVER :'.str_pad(number_format($shortOrOver, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            '  '.PHP_EOL,
            '---------------------------------------------------------------------------' . PHP_EOL,
        ];

        Journal::createJournalEntry(Carbon::now()->format('Ymd'), 'x_reading',  $_journal_x_reading);

        return response()->json([
            'report_date' => $reportDate,
            'report_time' => $reportTime,
            'time_in' => $time_in,
            'time_out' => $time_out,
            'user' => $user->name,
            'beginning_or' => $beginningOR,
            'ending_or' => $endingOR,
            'opening_fund' => $openingFund,
            'ending_fund' => $endingFund,
            'total_cash_payment' => $totalCashPayment,
            'total_gcash_payment' => $totalGcashPayment,
            'total_maya_payment' => $totalMayaPayment,
            'total_debit_payment' => $totalDebitPayment,
            'total_credit_payment' => $totalCreditPayment,
            'total_digital_payment' => $totalDigitalPayment,
            'total_credit_payment' => $totalCreditPayment,
            'total_payments' => $totalPayments,
            'void_value' => $voidValue,
            'refund_value' => $refundValue,
            'cash_in_drawer' => $cashInDrawer,
            'withdrawal' => $withdrawal,
            'less_withdrawal' => $lessWithdrawal,
            'short_or_over' => $shortOrOver,
        ], 200);
    }

    public function reprint(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'date' => 'required|string',
        ]);

        try {
            $parsedDate = Carbon::parse($request->date)->toDateString();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid date format. Please provide a valid date.'
            ], 422);
        }

        $latestXRecord = x_record::where('generated_by', $request->user_id)
            ->whereDate('created_at', $parsedDate)
            ->latest()
            ->first();

        // dd($latestXRecord);

        //Create Journal List
        $_journal_x_reading = [
            '  -----       REPRINT       ----  ',
            'Date :' . str_pad(now()->format('F d, Y'), 28, ' ', STR_PAD_LEFT),
            'Time :' . str_pad(now()->format('h:i A'), 28, ' ', STR_PAD_LEFT) . PHP_EOL,
            '   THERMOZONE PHILIPPINES CORP.   ',
            ' 2286 Marconi St., Brgy. San Isid ',
            '        ro City of Makati,        ',
            '  VAT REG TIN: 223-661-818-00000  ',
            '         MIN: '.'XXXXXXXXXX       ',
            '         S/N: '.'XXXXXXXXXX       ',
            ' ',
            '          X READING REPORT       ',
            ' Report Date: '.str_pad($latestXRecord->report_date, 19, ' ', STR_PAD_LEFT),
            ' Report Time: '.str_pad($latestXRecord->report_time, 19, ' ', STR_PAD_LEFT),
            ' ',
            ' Start Time: '.str_pad($latestXRecord->start_time, 20, ' ', STR_PAD_LEFT),
            ' End Time: '.str_pad($latestXRecord->end_time, 22, ' ', STR_PAD_LEFT),
            ' ',
            ' Cashier: '.str_pad($latestXRecord->cashier_name, 23, ' ', STR_PAD_LEFT),
            ' ',
            ' Beg. SI #: '.str_pad(is_null($latestXRecord->beginning_si) ? 'N/A' : str_pad($latestXRecord->beginning_si, 12, '0', STR_PAD_LEFT), 21, ' ', STR_PAD_LEFT),
            ' End SI #: '.str_pad(is_null($latestXRecord->ending_si) ? 'N/A' : str_pad($latestXRecord->ending_si, 12, '0', STR_PAD_LEFT), 22, ' ', STR_PAD_LEFT),
            ' ',
            ' Opening Fund'.str_pad(number_format($latestXRecord->opening_fund, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' ================================ ',
            'PAYMENTS RECIEVED                 ',
            ' CASH'.str_pad(number_format($latestXRecord->cash_payments, 2, '.', ''), 28, ' ', STR_PAD_LEFT),
            ' GCASH '.str_pad(number_format($latestXRecord->gcash_payments, 2, '.', ''), 26, ' ', STR_PAD_LEFT),
            ' MAYA '.str_pad(number_format($latestXRecord->maya_payments, 2, '.', ''), 27, ' ', STR_PAD_LEFT),
            ' DEBIT CARD'.str_pad(number_format($latestXRecord->debit_payments, 2, '.', ''), 22, ' ', STR_PAD_LEFT),
            ' CREDIT CARD'.str_pad(number_format($latestXRecord->credit_payments, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' Total Payments:'.str_pad(number_format($latestXRecord->total_payments, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' ================================ ',
            ' VOID: '.str_pad(number_format($latestXRecord->void, 2, '.', ''), 26, ' ', STR_PAD_LEFT),
            ' ================================ ',
            ' WITHDRAWAL: '.str_pad(number_format($latestXRecord->withdrawal, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' ================================ ',
            'TRANSACTION SUMMARY               ',
            ' CASH IN DRAWER'.str_pad(number_format($latestXRecord->cash_in_drawer, 2, '.', ''), 18, ' ', STR_PAD_LEFT),
            ' GCASH '.str_pad(number_format($latestXRecord->gcash_payments, 2, '.', ''), 26, ' ', STR_PAD_LEFT),
            ' MAYA '.str_pad(number_format($latestXRecord->maya_payments, 2, '.', ''), 27, ' ', STR_PAD_LEFT),
            ' DEBIT CARD'.str_pad(number_format($latestXRecord->debit_payments, 2, '.', ''), 22, ' ', STR_PAD_LEFT),
            ' CREDIT CARD'.str_pad(number_format($latestXRecord->credit_payments, 2, '.', ''), 21, ' ', STR_PAD_LEFT),
            ' Opening Fund'.str_pad(number_format($latestXRecord->opening_fund, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' WITHDRAWAL :'.str_pad(number_format($latestXRecord->withdrawal, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            ' Less Withdrawal'.str_pad(number_format($latestXRecord->less_withdrawal, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' Payments Receiv'.str_pad(number_format($latestXRecord->total_payments, 2, '.', ''), 17, ' ', STR_PAD_LEFT),
            ' ================================ ',
            ' SHORT/OVER :'.str_pad(number_format($latestXRecord->short_over, 2, '.', ''), 20, ' ', STR_PAD_LEFT),
            '  '.PHP_EOL,
            '---------------------------------------------------------------------------' . PHP_EOL,
        ];

        Journal::createJournalEntry(Carbon::now()->format('Ymd'), 'x_reading_reprint',  $_journal_x_reading);

        if (!$latestXRecord) {
            return response()->json([
                'message' => 'No X Reading record found for the given user and date.'
            ], 404);
        }

        return response()->json($latestXRecord, 200);
    }


}
