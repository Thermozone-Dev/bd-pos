<?php

namespace App\Models;

use App\Traits\TransactionSummary;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class TransactionSummaryData extends Model
{
    use TransactionSummary, Sushi;

    public static $date = null;
    public static $user = null;

    public function filterQueryData($date = null, $user = null)
    {
        self::$date = $date;
        self::$user = $user;
        return self::query();
    }


    public function getRows(): array
    {
        // Process the transactions to create a summary of products
        $product_summary = [];

        $transaction = Transaction::withoutGlobalScopes()
            ->where('is_valid', true);

        $data = $this->getData($transaction);

        if(!empty($data)){
            $result = collect();
            if(!empty($data['detailed_products']) || !empty($data['detailed_packages']) ){
                $data['detailed_products']->map(function ($item) use ($result){
                    $result->push([
                        'name' => $item['name'],
                        'processed_by' => $item['user'],
                        'created_at' => $item['date'],
                        'price' => $item['price'],
                        'qty' => $item['quantity'],
                        'total' => $item['gross_income'],
                    ]);
                    return;
                });
                $data['detailed_packages']->map(function ($item) use ($result){
                    $result->push([
                        'name' => $item['name'],
                        'processed_by' => $item['user'],
                        'created_at' => $item['date'],
                        'price' => $item['price'],
                        'qty' => $item['quantity'],
                        'total' => $item['gross_income'],
                    ]);
                    return;
                });
                $product_summary = $result->toArray();
            }
        }

        return array_values($product_summary);
    }

    protected function getSchema(): array
    {
        return [
            'name' => 'string',
            'processed_by' => 'string',
            'created_at' => 'datetime',
            'price' => 'float',
            'qty' => 'integer',
            'total' => 'float',
        ];
    }



    protected function sushiShouldCache()
    {
        return true;
    }
}
