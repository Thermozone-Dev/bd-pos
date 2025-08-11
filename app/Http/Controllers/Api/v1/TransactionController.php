<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Item;
use App\Models\NacInfo;
use App\Models\Product;
use App\Models\PwdInfo;
use App\Models\ScInfo;
use App\Models\SoloparentInfo;
use App\Models\Stub;
use App\Models\Transaction;
use App\Models\TransactionBasket;
use App\Models\TransactionBasketHasDiscount;
use App\Models\TransactionBasketItem;
use App\Models\TransactionBasketItemHasDiscount;
use App\Models\VoidTransaction;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Array_;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;
use App\Traits\TransactionSummary;
use BezhanSalleh\FilamentShield\Support\Utils;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth;

class TransactionController extends Controller
{

    use TransactionSummary;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $_transactions = Transaction::all();
        return response()->json($_transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // return response()->json($request->transaction_fee, 201);
            //Validate Request
            $request->validate([
                'items.*.item_id' => 'numeric',
                'items.*.item_quantity' => 'numeric',
                'items.*.item_discounts.id' => 'numeric',
                'transaction_discounts.id' => 'numeric',
                'transaction_method' => 'required|numeric',
                'transaction_fee' => 'required|numeric',
                'reference_number' => 'string',
                'total_sales' => 'required|numeric',
                'cash_tendered' => 'required|numeric',
                'change' => 'required|numeric',
                'gross_sales' => 'required|numeric',
                'vatable_sales' => 'required|numeric',
                'vat' => 'required|numeric',
                'vat_exempt_sales' => 'required|numeric',
                'vat_deduction' => 'required|numeric',
                'vat_adjustment' => 'required|numeric',
                'zero_rated_sales' => 'required|numeric',
                'gov_discount_details.*.name' => 'string',
                'gov_discount_details.*.id' => 'numeric',
                'gov_discount_details.*.tin' => 'numeric',
                'gov_discount_details.*.child_name' => 'string',
                'gov_discount_details.*.child_age' => 'numeric',
                'gov_discount_details.*.child_birthday' => 'date|date_format:Y-m-d',
            ]);

            $_data = [
                'processed_by' => auth()->user()->id,
                'transaction_basket_id' => null,
                'barcode' =>  null,
                'transaction_method_id' => $request->transaction_method,
                'transaction_fee' => $request->transaction_fee,
                'reference_number' => $request->reference_number,
                'cash_tendered' => $request->cash_tendered,
                'total_sales' => $request->total_sales,
                'change' => $request->change,
                'gross_sales' => $request->gross_sales,
                'vatable_sales' => $request->vatable_sales,
                'vat' => $request->vat,
                'vat_exempt_sales' => $request->vat_exempt_sales,
                'vat_deduction' => $request->vat_deduction,
                'vat_adjustment' => $request->vat_adjustment,
                'zero_rated_sales' => $request->zero_rated_sales,
                'is_valid' => true,
                'is_pwd' => false,
                'is_sc' => false,
                'is_nac' => false,
                'is_soloparent' => false,
            ];

            //Create Basket
            $_basket = TransactionBasket::create();
            $_data['transaction_basket_id'] = $_basket->id;

            //Create Basket Items
            $_gov_discount_list = [];
            $_basket_item_data = $this->processItems($_basket, $request->items, $_gov_discount_list);


            //return these values
            $_basket_items = $_basket_item_data[0];
            $_gov_discount_list = $_basket_item_data[1];

            // Per Basket Discount
            if(!empty($item['item_discounts'])) {
                if (!(count($request->transaction_discounts) === 0)) {
                    $_discount = Discount::find($request->transaction_discounts['id']);

                    $_basket_has_discount_data = [
                        'transaction_basket_id' => $_basket->id,
                        'discount_id' => $_discount->id,
                    ];
                    $_basket_has_discount = TransactionBasketHasDiscount::create($_basket_has_discount_data);

                    //Add Gov Discount Processing
                    $_gov_discount_list = $this->getDiscounts($request->transaction_discounts, $_gov_discount_list);
                }
            }


            //Create Transaction
            $_transaction = Transaction::create($_data);
            $_transaction->barcode = $_transaction->id;

            //Gov discount processing
            if(!empty($_gov_discount_list)){
                if(isset($request->gov_discount_details)){
                    foreach ($request->gov_discount_details as $key => $details) {
                        match ($key) {
                                'sc' => $this->createScInfo($details, $_transaction->id),
                                'pwd' => $this->createPwdInfo($details, $_transaction->id),
                                'nac' => $this->createNacInfo($details, $_transaction->id),
                                'sp' => $this->createSpInfo($details, $_transaction->id),
                        };
                        match ($key) {
                                'sc' => $_transaction->is_sc = true,
                                'pwd' => $_transaction->is_pwd = true,
                                'nac' => $_transaction->is_nac = true,
                                'sp' => $_transaction->is_soloparent = true,
                        };
                    }
                }
                else{
                    $_err = ['error' => 'Missing Discount Details',];
                    return response()->json($_err, 400);
                }
            }

            $_transaction->update();

            //Process Data Formatting for json
            $_return_data = [
                'transaction details' => $_transaction,
                'transaction basket' => $_basket_items,
                'stub_details' => $this->generate_stub($_transaction->id),

            ];

            return response()->json($_return_data, 201);

        } catch (Exception $err) {
            return response()->json($err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $_transaction = Transaction::find($id);
        return response()->json($_transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $_transaction = Transaction::findFirst($id);
        $_transaction->update($request->all());
        return response()->json($_transaction, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Transaction::destroy($id);
        return response()->json(null, 204);
    }

    private function createPwdInfo($details, $transaction_id){
        $_data = [
            'transaction_id' => $transaction_id,
            'name' => $details['name'],
            'pwd_id' => $details['id'],
            'pwd_tin' => $details['tin'] ?? null,
        ];

        PwdInfo::create($_data);
    }

    private function createScInfo($details, $transaction_id){
        $_data = [
            'transaction_id' => $transaction_id,
            'name' => $details['name'],
            'sc_id' => $details['id'],
            'sc_tin' => $details['tin'] ?? null,
        ];

        ScInfo::create($_data);
    }

    private function createNacInfo($details, $transaction_id){
        $_data = [
            'transaction_id' => $transaction_id,
            'name' => $details['name'],
            'pnstm_id' => $details['id'],
        ];

        NacInfo::create($_data);
    }

    private function createSpInfo($details, $transaction_id){
        $_data = [
            'transaction_id' => $transaction_id,
            'name' => $details['name'],
            'spic_id' => $details['id'],
            'child_name' => $details['child_name'] ?? null,
            'child_age' => $details['child_age'] ?? null,
            'child_birthday' => Carbon::parse($details['child_birthday'])->format('Y-m-d') ?? null,
        ];

        SoloparentInfo::create($_data);
    }

    private function getDiscounts($data, $gov_discount_list): array
    {
        $_gov_discount = match ($data) {
            1 => 'sc',
            2 => 'pwd',
            3 => 'nac',
            4 => 'sp',
            default => null,

        };

        if (!is_null($_gov_discount) || !in_array($_gov_discount, $gov_discount_list)){
            array_push($gov_discount_list, $_gov_discount);
        }
        return $gov_discount_list;
    }

    private function processItems(TransactionBasket $basket, array $items, array $gov_discount_list)
    {
        $_basket_items = [];
        foreach($items as $item){
            $_item_data = [
                'transaction_basket_id' => $basket['id'],
                'item_id' => $item['item_id'],
                'quantity' => $item['item_quantity'],
                'discount_value' => $item['discount_value'],
                'total_value' => $item['total_value'],
            ];

            $_basket_item = TransactionBasketItem::create($_item_data);

            // Discount Check
            // Per Item Discounts
            if(!empty($item['item_discounts'])) {
                if (!(count($item['item_discounts']) === 0)) {
                    $data = $item['item_discounts']['id'];

                    $_discount = Discount::find($data);

                    $_item_has_discount_data = [
                        'transaction_basket_item_id' => $_basket_item->id,
                        'discount_id' => $_discount->id,
                    ];
                    $_item_has_discount = TransactionBasketItemHasDiscount::create($_item_has_discount_data);

                    //Add Gov Discount Processing
                    $gov_discount_list = $this->getDiscounts($data, $gov_discount_list);
                }
            }

            $_basket_item->update($_item_data);
            array_push($_basket_items, $_basket_item);

        }
        return [$_basket_items, $gov_discount_list];
    }

    public function voidTransaction(Request $request, string $id)
    {
        try {
            $_transaction = Transaction::find($id);
            if ($_transaction) {
                $_transaction->is_valid = false;
                $_transaction->update();

                VoidTransaction::create([
                    'transaction_id' => $_transaction->id,
                ]);

                return response()->json(['message' => 'Transaction voided successfully.'], 200);

            } else {
                return response()->json(['error' => 'Transaction not found.'], 404);
            }
        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }

    public function restoreTransaction(Request $request, string $id)
    {
        try {
            $_transaction = Transaction::find($id);
            if ($_transaction) {
                $_transaction->is_valid = true;
                $_transaction->update();

                $voidTransaction = VoidTransaction::where('transaction_id', $_transaction->id)->first();
                if ($voidTransaction) {
                    $voidTransaction->delete();
                }

                return response()->json(['message' => 'Transaction restored successfully.'], 200);
            } else {
                return response()->json(['error' => 'Transaction not found.'], 404);
            }
        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }

    public function dailySummary(){

         try {
            if(!auth()->check()){
                return response()->json(['error' => 'Unauthrorized.'], 401);
            }
            $response = response()->json([], 200);

            $data = $this->transactionSummary('today');

            if(!empty($data)){
                $result = collect();
                if(!empty($data['products_excluded_in_package']) || !empty($data['packages']) ){
                    $data['packages']->map(function ($item) use ($result){
                        $result->push([
                            'name' => $item['name'],
                            'price' =>$item['price'],
                            'qty' => $item['quantity'],
                            'total' => $item['quantity'] * $item['price'],
                        ]);
                        return;
                    });
                    $data['products_excluded_in_package']->map(function ($item) use ($result){
                        $result->push([
                            'name' => $item['name'],
                            'price' =>$item['price'],
                            'qty' => $item['quantity'],
                            'total' => $item['quantity'] * $item['price'],
                        ]);
                        return;
                    });
                    $response = response()->json($result->toArray(), 200);
                }
            }

            return $response;

        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }

    }


    public function rolebaseDailysummary(){
        try {
            if(!auth()->check()){
                return response()->json(['error' => 'Unauthrorized.'], 401);
            }

            $response = $this->dailySummary();
            $_user = auth()->user();

            if($_user->hasRole('Cashier')){

                $transaction = Transaction::withoutGlobalScopes()
                    ->where('is_valid', true)
                    ->whereDate('created_at', Carbon::today())
                    ->where('processed_by', $_user->id);

                $data = $this->getData($transaction);
                if(!empty($data)){
                    $result = collect();
                    if(!empty($data['products_excluded_in_package']) || !empty($data['packages']) ){
                        $data['packages']->map(function ($item) use ($result){
                            $result->push([
                                'name' => $item['name'],
                                'price' =>$item['price'],
                                'qty' => $item['quantity'],
                                'total' => $item['quantity'] * $item['price'],
                            ]);
                            return;
                        });
                        $data['products_excluded_in_package']->map(function ($item) use ($result){
                            $result->push([
                                'name' => $item['name'],
                                'price' =>$item['price'],
                                'qty' => $item['quantity'],
                                'total' => $item['quantity'] * $item['price'],
                            ]);
                            return;
                        });
                        $response = response()->json($result->toArray(), 200);
                    }
                }
            }

            return $response;

        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }

    public function claim_stub(Request $request){
        try {
            $request->validate([
                'stub_no' => 'required',
            ]);

            $stub = Stub::where('stub_no', $request->stub_no)->first();

            if ($stub) {
                $stub->status = 1; // Mark as claimed
                $stub->claimed_at = now();
                $stub->save();


                $response = [
                    'date' => $stub->transaction->created_at->format('F j, Y'),
                    'time' => $stub->transaction->created_at->format('h:i:s A'),
                    'transaction_no' => $stub->transaction->id,
                    'stub' => $stub->stub_no,
                    'quantity' => $stub->quantity,
                    'price' => $stub->packageInclusive->price,
                    'pack_inclusive_name' => $stub->packageInclusive->name,
                    'items' => $stub->packageInclusive->packageInclusiveProducts->map(function ($product) {
                        return [
                            'name' => $product->product->name,
                            'quantity' => $product->qty,
                        ];
                    }),
                ];

                return response()->json($response, 200);
            } else {
                return response()->json(['error' => 'Stub not found.'], 404);
            }
        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }

    public function generate_stub($transaction_id){
        try {
            $_transaction = Transaction::find($transaction_id);
            //add has inclusion if has Item
            if ($_transaction) {
                $items = Item::whereIn('id', $_transaction->basket->items->pluck('item_id'))
                    ->where('package_id', '!=', null)
                    ->get();

                $_basket_item_quantity = $_transaction->basket->items->pluck('quantity', 'item_id');

                foreach ($items as $item) {
                    $qty = $_basket_item_quantity[$item->id];
                    if(!empty($item->package->packageHasPackageInclusive)){
                        $inclusions = $item->package->packageHasPackageInclusive->map(function ($packageInclusion) use ($qty){
                            return [
                                'id' => $packageInclusion->id,
                                'name' => $packageInclusion->packageInclusive->name,
                                'quantity' => $qty,
                                'price' => $packageInclusion->packageInclusive->price,
                                'description' => $packageInclusion->packageInclusive->description,
                                'items' => $packageInclusion->packageInclusive->packageInclusiveProducts->map(function ($product) {
                                    return [
                                        'name' => $product->product->name,
                                    ];
                                }),
                            ];
                        });
                    }
                }
                foreach ($inclusions as $inclusion){
                    $stub_no = null;
                    while (true) {
                        // Check if the stub number already exists
                        $stub_no = now()->format('mdY-Hisv');
                        if (!Stub::where('stub_no', $stub_no)->first()) {
                            break;
                        }
                    }
                    $stub_details = [
                        'transaction_id' => $_transaction->id,
                        'package_inclusive_id' => $inclusion['id'],
                        'quantity' => $inclusion['quantity'],
                        'stub_no' =>  $stub_no,
                        'created_by' => auth()->user()->id ?? null,
                    ];
                    $stub = Stub::create($stub_details);
                }
                $_stubs = Stub::where('transaction_id', $_transaction->id)
                    ->where('status', 0)
                    ->get();
                $_stubs = $_stubs->map(function ($stub){
                        return [
                            'stub_no'=> $stub->stub_no,
                            'name' => $stub->packageInclusive->name,
                            'quantity' => $stub->quantity,
                            'price' => $stub->packageInclusive->price,
                            'items' => $stub->packageInclusive->packageInclusiveProducts->map(function ($product) {
                                return [
                                    'name' => $product->product->name,
                                    'qty' => $product->qty,
                                ];
                            }),
                        ];
                    });

                return [
                    'has_inclusive' => true,
                    'stubs' =>  $_stubs
                ];
            }
        } catch (Exception $err) {
            return [
                'has_inclusive' => false,
                'stubs' =>  []
            ];
        }
    }

}

