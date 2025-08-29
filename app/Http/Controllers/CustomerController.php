<?php

namespace App\Http\Controllers;

use App\Models\NacInfo;
use App\Models\PwdInfo;
use App\Models\ScInfo;
use App\Models\SoloparentInfo;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function retrieveCustomerData(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
        ]);

        $transaction = Transaction::query()->where('id', $request->transaction_id)->first();
        $data = [];

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->is_sc == true) {
            $scInfo = ScInfo::query()->where('transaction_id', $transaction->id)->first();
            $data = [
                'transaction_id' => $transaction->id,
                'name' => $scInfo->name,
                'id' => $scInfo->sc_id,
            ];
        }

        else if ($transaction->is_pwd == true) {
            $pwdInfo = PwdInfo::query()->where('transaction_id', $transaction->id)->first();
            $data = [
                'transaction_id' => $transaction->id,
                'name' => $pwdInfo->name,
                'id' => $pwdInfo->pwd_id,
            ];
        }

        else if ($transaction->is_nac == true) {
            $nacInfo = NacInfo::query()->where('transaction_id', $transaction->id)->first();
            $data = [
                'transaction_id' => $transaction->id,
                'name' => $nacInfo->name,
                'id' => $nacInfo->pnstm_id,
            ];
        }

        elseif ($transaction->is_soloparent == true) {
            $soloparentInfo = SoloparentInfo::query()->where('transaction_id', $transaction->id)->first();
            $data = [
                'transaction_id' => $transaction->id,
                'name' => $soloparentInfo->name,
                'id' => $soloparentInfo->spic_id,
            ];
        }

        return response()->json($data);

    }
}
