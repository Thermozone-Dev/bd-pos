<?php

namespace App\Http\Controllers\Api\v1;

use App\Filament\Loggers\ShiftLogger;
use App\Filament\Loggers\TransactionLogger;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\x_record;
use App\Models\XReading;
use Carbon\Carbon;
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

        $time_in = Carbon::parse($shift->time_in)->format('h:i A');
        $time_out = Carbon::now()->format('h:i A');

        $transactions = Transaction::where('created_at', '>=', $inTime)->where('processed_by', $user->id)->get();

        $startOR = $transactions->first()?->id ?? null;
        $beginningOR = str_pad($startOR, 12, '0', STR_PAD_LEFT);
        $endOR = $transactions->last()?->id ?? null;
        $endingOR = str_pad($endOR, 12, '0', STR_PAD_LEFT);

        $openingFund = $shift->opening_balance;
        $endingFund = $shift->ending_balance;

        $totalChange = $transactions->where('transaction_method_id', 1)->where('is_valid', true)->sum('change');

        $totalCashPayment = $transactions->where('transaction_method_id', 1)->where('is_valid', true)->sum('total_sales');
        $totalGcashPayment = $transactions->where('transaction_method_id', 2)->where('is_valid', true)->sum('total_sales');
        $totalMayaPayment = $transactions->where('transaction_method_id', 3)->where('is_valid', true)->sum('total_sales');
        $totalDebitPayment = $transactions
            ->where('transaction_method_id', 4)
            ->where('is_valid', true)
            ->sum('total_sales');
        $totalCreditPayment = $transactions
            ->where('transaction_method_id', 5)
            ->where('is_valid', true)
            ->sum('total_sales');

        $totalDigitalPayment = $transactions
            ->whereNotIn('transaction_method_id', [1, 5])
            ->where('is_valid', true)
            ->sum('total_sales');

        $totalPayments = $transactions->sum('total_sales');

        $voidValue = $transactions
            ->where('is_valid', false)
            ->sum('total_sales');

        $refundValue = 0;

        $cashInDrawer = $openingFund + $totalCashPayment;

        $withdrawal = $totalChange;
        $lessWithdrawal = $cashInDrawer - $totalChange;

        $shortOrOver = $request->currentCash - $cashInDrawer;

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

        if (!$latestXRecord) {
            return response()->json([
                'message' => 'No X Reading record found for the given user and date.'
            ], 404);
        }

        return response()->json($latestXRecord, 200);
    }


}
