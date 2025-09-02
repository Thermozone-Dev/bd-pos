<?php

namespace App\Http\Controllers\Api\v1;

use App\Filament\Loggers\ShiftLogger;
use App\Filament\Loggers\TransactionLogger;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
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

        $totalDigitalPayment = $transactions
            ->whereNotIn('transaction_method_id', [1, 5])
            ->where('is_valid', true)
            ->sum('total_sales');

        $totalCreditPayment = $transactions
            ->where('transaction_method_id', 5)
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

}
