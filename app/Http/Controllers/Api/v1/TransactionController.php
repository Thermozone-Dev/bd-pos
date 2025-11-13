<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Journal\Journal;
use App\Models\Discount;
use App\Models\Item;
use App\Models\NacInfo;
use App\Models\Package;
use App\Models\PaymentMethod;
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
use App\Models\TransactionHasPaymentMethod;
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
use PHPUnit\Event\Runtime\PHP;

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
                'terminal_id' => 'required|numeric',
                'items.*.item_id' => 'numeric',
                'items.*.item_quantity' => 'numeric',
                'items.*.item_discounts.id' => 'numeric',
                'transaction_discounts.id' => 'numeric',
                'transaction_methods.*.transaction_method_id' => 'required|numeric',
                'transaction_methods.*.cash_tendered' => 'required|numeric',
                'transaction_methods.*.reference_number' => 'string|numeric',
                'total_transaction_fee' => 'required|numeric',
                'reference_number' => 'string',
                'total_cash_tendered' => 'required|numeric',
                'total_sales' => 'required|numeric',
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
                'terminal_id' => (int)$request->terminal_id,
                'transaction_basket_id' => null,
                'barcode' =>  null,
                'transaction_method_id' => 0,
                'total_transaction_fee' => round($request->total_transaction_fee, 2),
                'total_cash_tendered' => round($request->total_cash_tendered, 2),
                'total_sales' => round($request->total_sales, 2),
                'change' => round($request->change, 2),
                'gross_sales' => round($request->gross_sales, 2),
                'vatable_sales' => round($request->vatable_sales, 2),
                'vat' => round($request->vat, 2),
                'vat_exempt_sales' => round($request->vat_exempt_sales, 2),
                'vat_deduction' => round($request->vat_deduction, 2),
                'vat_adjustment' => round($request->vat_adjustment, 2),
                'zero_rated_sales' => round($request->zero_rated_sales, 2),
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

            $_transaction_methods = [];
            $_method_list = [];
            foreach ($request->transaction_methods as $method){
                array_push($_transaction_methods, TransactionHasPaymentMethod::create(
                    [
                        'transaction_id' => $_transaction->id,
                        'payment_method_id' => $method['transaction_method_id'],
                        'cash_tendered' => round($method['cash_tendered'], 2),
                        'transaction_fee' => round($method['transaction_fee'] ?? 0, 2),
                        'reference_number' => $method['reference_number'] ?? '00000000',
                    ]
                ));
                array_push($_method_list, PaymentMethod::find($method['transaction_method_id'])->name);
            }

            $_transaction->update();

            //Create Journal List
            $_journal_transaction_details = [
                '  -----       INVOICE       ----  ',
                ' ',
                '   Thermozone Philippines Corp.   ',
                ' 2286 Marconi St., Brgy. San Isid ',
                '        ro City of Makati,        ',
                '  VAT REG TIN: 223-661-818-00000  ',
                ' ',
                ' -------------------------------- ',
                ' ',
                ' MIN: '.'XXXXXXXXXX   ',
                ' Serial No : ' . 'XXXXXXXXXX   ',
                ' ',
                'Issued By : ' . auth()->user()->name,
                'Invoice NO : ' . str_pad($_transaction->id, 12, '0', STR_PAD_LEFT),
                'Date : ' . $_transaction->created_at->format('F d, Y'),
                'Payment Method : ' . implode(', ' , $_method_list),
                ' ',
                '----------------------------------',
            ];

            $_journal_customer_details = [
                ' ',
                ' -----   CUSTOMER DETAILS  ----- ',
                ' ',
            ];

            if ($_transaction->is_sc) {
                $_sc_info = ScInfo::where('transaction_id', $_transaction->id)->first();
                array_push($_journal_customer_details,
                    ' Name: ' . $_sc_info->name,
                    ' ID Number  : ' . $_sc_info->sc_id,
                    ' Signature : ______________________ '
                );
            }
            else if ($_transaction->is_pwd) {
                $_pwd_info = PwdInfo::where('transaction_id', $_transaction->id)->first();
                array_push($_journal_customer_details,
                    ' Name:      ' . $_pwd_info->name,
                    ' ID Number: ' . $_pwd_info->pwd_id,
                    ' Signature: ______________________ '
                );
            }
            else if ($_transaction->is_nac) {
                $_nac_info = NacInfo::where('transaction_id', $_transaction->id)->first();
                array_push($_journal_customer_details,
                    ' Name:      ' . $_nac_info->name,
                    ' ID Number: ' . $_nac_info->pnstm_id,
                    ' Signature: ______________________ '
                );
            }
            else if ($_transaction->is_soloparent) {
                $_sp_info = SoloparentInfo::where('transaction_id', $_transaction->id)->first();
                array_push($_journal_customer_details,
                    ' Name:      ' . $_sp_info->name,
                    ' ID Number: ' . $_sp_info->spic_id,
                    ' Signature: ______________________ '
                );
            }
            else {
                array_push($_journal_customer_details,
                    ' Name:      ______________________ ',
                    ' Address:   ______________________ ',
                    ' TIN:       ______________________ ',
                    ' Signature: ______________________ '
            );
            }

            $_journal_item_details = [
                ' ',
                '----------------------------------',
                ' ',
                ' -----    ITEM BREAKDOWN    ----- ',
                ' ',
                ' Qty     Item     Price     Total ',
            ];
            $_discount_value = 0.0;
            $_item_count = 0;
            foreach ($_basket_items as $_basket_item) {
                $_item = Item::find($_basket_item->item_id);
                if($_item->package){
                    $_item_data = Package::find($_item->package_id);
                }
                if($_item->product){
                    $_item_data = Product::find($_item->product_id);
                }
                array_push($_journal_item_details,
                    ' ' . $_basket_item->quantity .
                    '     ' . $_item_data->name .
                    '    @' . number_format($_item_data->price, 2) .
                    '     ' . number_format($_basket_item->total_value, 2));
                $_discount_value += $_basket_item->discount_value;
                $_item_count += $_basket_item->quantity;
            }

            $_journal_discount_details = [''];

            if($_discount_value > 0){
                array_push($_journal_discount_details,[
                    '----------------------------------',
                    '',
                    $_item_count . ' Item(s)',
                    ' Subtotal:      ' . str_pad(number_format($_transaction->gross_sales + $_transaction->vat_adjustment, 2), 15, ' ', STR_PAD_LEFT),
                    '',
                    '----------------------------------',
                    '',
                    ' Less Disc Vat: ' . str_pad(number_format($_transaction->vat_adjustment, 2), 15, ' ', STR_PAD_LEFT),
                    ' Gross Total:   ' . str_pad(number_format($_transaction->gross_sales, 2), 15, ' ', STR_PAD_LEFT),
                    '',
                ]);
                $_discount_string = match(true) {
                    $_transaction->is_sc == true =>
                        ' Less SC @ 20%:  ' . str_pad(number_format($_discount_value, 2), 15, ' ', STR_PAD_LEFT),
                    $_transaction->is_pwd == true =>
                        ' Less PWD @ 20%: ' . str_pad(number_format($_discount_value, 2), 15, ' ', STR_PAD_LEFT),
                    $_transaction->is_nac == true =>
                        ' Less NAC @ 20%: ' . str_pad(number_format($_discount_value, 2), 15, ' ', STR_PAD_LEFT),
                    $_transaction->is_soloparent == true =>
                        ' Less SP @ 20%:  ' . str_pad(number_format($_discount_value, 2), 15, ' ', STR_PAD_LEFT),
                    $_transaction->is_soloparent == true =>
                        ' Less Promo:  ' . str_pad(number_format($_discount_value, 2), 15, ' ', STR_PAD_LEFT),
                };
                $_journal_sales_details = [
                    '----------------------------------',
                    ' ',
                    $_discount_string
                ];
            }
            else{
                $_journal_sales_details = [
                    '----------------------------------',
                    ' ',
                    'Gross Sales   : ' . str_pad(number_format($_transaction->gross_sales ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                ];
            }

            $_journal_sales_details = array_merge($_journal_sales_details, [
                'Cash Tendered : ' . str_pad(number_format($_transaction->total_cash_tendered ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                'VATable Sales : ' . str_pad(number_format($_transaction->vatable_sales ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                'Change : ' . str_pad(number_format($_transaction->change ?? 0, 2), 25, ' ', STR_PAD_LEFT),
                'VAT : ' . str_pad(number_format($_transaction->vat ?? 0, 2), 28, ' ', STR_PAD_LEFT),
                'VAT Exempt Sales : ' . str_pad(number_format($_transaction->vat_exempt_sales ?? 0, 2), 15, ' ', STR_PAD_LEFT),
                'Zero Rated Sales : ' . str_pad(number_format($_transaction->zero_rated_sales ?? 0, 2), 15, ' ', STR_PAD_LEFT),
                'Amount Due :  ' . str_pad(number_format($_transaction->total_sales ?? 0, 2), 20, ' ', STR_PAD_LEFT),
                '',
                '           ' . str_pad($_transaction->id, 12, '0', STR_PAD_LEFT) . '     ',
                '',
                '   THERMOZONE PHILIPPINES CORP.   ',
                '   2286 Marconi St. Makati City   ',
                '  VAT REG TIN: 223-661-818-00000  ',
                ' Accreditation Number: XXXXXXXXXX ',
                '      ATG Number: XXXXXXXXXX      ' . PHP_EOL,
            ]);


            $_journal_list = array_merge(
                $_journal_transaction_details,
                $_journal_customer_details,
                $_journal_item_details,
                $_journal_discount_details,
                $_journal_sales_details
            );

            dd($_journal_list);
            Journal::createJournalEntry(Carbon::now()->format('Ymd'), 'invoice',  $_journal_list);

            //Process Data Formatting for json
            $_return_data = [
                'transaction details' => $_transaction,
                'transaction basket' => $_basket_items,
                'payment methods' => $_transaction_methods,
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
                'discount_value' => round($item['discount_value'], 2),
                'total_value' => round($item['total_value'], 2),
                'package_base_price' =>  0.0,
            ];

            $test = Item::find($item['item_id']);
            $test = $test?->package;
            if($test){
                $_item_data['package_base_price'] = $test->base_price;
            }

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
                if (!$_transaction->is_valid) {
                    return response()->json(['error' => 'Transaction is already voided.'], 400);
                } else {
                    $_transaction->is_valid = false;
                    $_transaction->update();

                    $void = VoidTransaction::create([
                        'transaction_id' => $_transaction->id,
                    ]);

                    $_transaction = Transaction::with([
                        'basket.items.item.product',
                        'basket.items.item.package'
                    ])->find($id);

                    if (!$_transaction) {
                        return response()->json(['error' => 'Transaction not found.'], 404);
                    }

                    $_method_list = [];
                    foreach($_transaction->paymentMethods as $method){
                        array_push($_method_list, PaymentMethod::find($method->payment_method_id)->name);
                    }

                    $transactionDetails = [
                        'id' => $_transaction->id,
                        'void_id' => str_pad($void->id, 12, '0', STR_PAD_LEFT),
                        'void_date' => $void->created_at->format('F j, Y'),
                        'void_time' => $void->created_at->format('h:i A'),
                        'processed_by' => $_transaction->processedBy->name,
                        'si_no' => str_pad($_transaction->id, 12, '0', STR_PAD_LEFT),
                        'date' => $_transaction->created_at->format('F j, Y'),
                        'time' => $_transaction->created_at->format('h:i A'),
                        'payment_method' => implode(', ' , $_method_list),

                        'is_sc' => $_transaction->is_sc,
                        'is_pwd' => $_transaction->is_pwd,
                        'is_nac' => $_transaction->is_nac,
                        'is_soloparent' => $_transaction->is_soloparent,

                        'gross_sales' => number_format($_transaction->gross_sales, 2),
                        'cash_tendered' => number_format($_transaction->total_cash_tendered, 2),
                        'vatable_sales' => number_format($_transaction->vatable_sales, 2),
                        'change' => number_format($_transaction->change, 2),
                        'vat' => number_format($_transaction->vat, 2),
                        'vat_exempt_sales' => number_format($_transaction->vat_exempt_sales, 2),
                        'zero_rated_sales' => number_format($_transaction->zero_rated_sales, 2),
                        'total_sales' => number_format($_transaction->total_sales, 2),
                    ];

                    $basketItems = $_transaction->basket->items
                        // ->filter(fn($basketItem) => $basketItem->discount_value <= 0)
                        ->map(function ($basketItem) {
                            return [
                                'id' => $basketItem->item->id,
                                'type' => $basketItem->item->type,
                                'name' => $basketItem->item->type === 'product'
                                    ? $basketItem->item->product?->name
                                    : $basketItem->item->package?->name,
                                'quantity' => $basketItem->quantity,
                                'price' => $basketItem->item->type === 'product'
                                    ? $basketItem->item->product?->price
                                    : $basketItem->item->package?->price,
                            ];
                        })->values();

                    $discountedBasketItems = $_transaction->basket->items
                        ->filter(fn($basketItem) => $basketItem->discount_value > 0)
                        ->map(function ($basketItem) {
                            return [
                                'id' => $basketItem->item->id,
                                'type' => $basketItem->item->type,
                                'name' => $basketItem->item->type === 'product'
                                    ? $basketItem->item->product?->name
                                    : $basketItem->item->package?->name,
                                'quantity' => $basketItem->quantity,
                                'price' => $basketItem->item->type === 'product'
                                    ? $basketItem->total_value
                                    : $basketItem->total_value,
                                'discount_value' => number_format($basketItem->discount_value, 2),
                            ];
                        })->values();


                        //Create Journal List
                        $_journal_transaction_details = [
                            '  -----    VOID INVOICE     ----  ',
                            '   Thermozone Philippines Corp.   ',
                            ' 2286 Marconi St., Brgy. San Isid ',
                            '        ro City of Makati,        ',
                            '  VAT REG TIN: 223-661-818-00000  ',
                            ' -------------------------------- ',
                            ' MIN: '.'XXXXXXXXXX   ',
                            ' Serial No : ' . 'XXXXXXXXXX   ',
                            ' ',
                            'Issued By : ' . auth()->user()->name,
                            'Invoice NO : ' . str_pad($_transaction->id, 12, '0', STR_PAD_LEFT),
                            'Date : ' . $_transaction->created_at->format('F d, Y'),
                            'Payment Method : ' . implode(', ' , $_method_list),
                            '----------------------------------',
                            ' -----    ITEM BREAKDOWN    ----- ',
                            ' Qty     Item     Price     Total ',
                        ];

                        $_journal_item_details = [];

                        foreach ($basketItems as $_basket_item) {
                            $_item = Item::find($_basket_item['id']);
                            if($_item->package){
                                $_item_data = Package::find($_item->package_id);
                            }
                            if($_item->product){
                                $_item_data = Product::find($_item->product_id);
                            }
                            array_push($_journal_item_details,
                                ' ' . $_basket_item['quantity'] .
                                '    ' . $_item_data->name .
                                '    -' . number_format($_item_data->price, 2) .
                                '    -' . number_format($_item_data->total_value, 2));
                        }
                        $_journal_sales_details = [
                            '----------------------------------',
                            'Cash Tendered : ' . str_pad("-".number_format($_transaction->total_cash_tendered ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                            'VATable Sales : ' . str_pad("-".number_format($_transaction->vatable_sales ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                            'Change : ' . str_pad("-".number_format($_transaction->change ?? 0, 2), 25, ' ', STR_PAD_LEFT),
                            'VAT : ' . str_pad("-".number_format($_transaction->vat ?? 0, 2), 28, ' ', STR_PAD_LEFT),
                            'VAT Exempt Sales : ' . str_pad("-".number_format($_transaction->vat_exempt_sales ?? 0, 2), 15, ' ', STR_PAD_LEFT),
                            'Zero Rated Sales : ' . str_pad("-".number_format($_transaction->zero_rated_sales ?? 0, 2), 15, ' ', STR_PAD_LEFT),
                            'Total Sales : ' . str_pad("-".number_format($_transaction->total_sales ?? 0, 2), 20, ' ', STR_PAD_LEFT),
                            '',
                            '           ' . str_pad($_transaction->id, 12, '0', STR_PAD_LEFT) . '     ',
                            '',
                            '   THERMOZONE PHILIPPINES CORP.   ',
                            '   2286 Marconi St. Makati City   ',
                            '  VAT REG TIN: 223-661-818-00000  ',
                            ' Accreditation Number: XXXXXXXXXX ',
                            '      ATG Number: XXXXXXXXXX      ' . PHP_EOL,
                        ];

                        $_journal_list = array_merge(
                            $_journal_transaction_details,
                            $_journal_item_details,
                            $_journal_sales_details
                        );

                    Journal::createJournalEntry(Carbon::now()->format('Ymd'), 'void_invoice',  $_journal_list);

                    $response = [
                        'message' => 'Transaction voided successfully.',
                        'transaction_details' => $transactionDetails,
                        'items' => $basketItems,              // only items WITHOUT discounts
                        'discounted_items' => $discountedBasketItems, // only items WITH discounts
                    ];

                    return response()->json($response, 200);
                }

            } else {
                return response()->json(['error' => 'Transaction not found.'], 404);
            }
        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }

    public function reprintVoidTransaction(Request $request, string $id)
    {
        try {
            $_void = VoidTransaction::where('id', $id)->first();
            if (!$_void) {
                return response()->json(['error' => 'Void Transaction not found.'], 404);
            }

            $_id = $_void->transaction_id;
            $_transaction = Transaction::find($_id);
            if ($_transaction) {
                $_transaction = Transaction::with([
                    'basket.items.item.product',
                    'basket.items.item.package'
                ])->find($_id);

                if (!$_transaction) {
                    return response()->json(['error' => 'Transaction not found.'], 404);
                }

                $_method_list = [];
                foreach($_transaction->paymentMethods as $method){
                    array_push($_method_list, PaymentMethod::find($method->payment_method_id)->name);
                }

                $transactionDetails = [
                    'id' => $_transaction->id,
                    'void_id' => str_pad($_void->id, 12, '0', STR_PAD_LEFT),
                    'void_date' => $_void->created_at->format('F j, Y'),
                    'void_time' => $_void->created_at->format('h:i A'),
                    'processed_by' => $_transaction->processedBy->name,
                    'si_no' => str_pad($_transaction->id, 12, '0', STR_PAD_LEFT),
                    'date' => $_transaction->created_at->format('F j, Y'),
                    'time' => $_transaction->created_at->format('h:i A'),
                    'payment_method' => implode(', ' , $_method_list),

                    'is_sc' => $_transaction->is_sc,
                    'is_pwd' => $_transaction->is_pwd,
                    'is_nac' => $_transaction->is_nac,
                    'is_soloparent' => $_transaction->is_soloparent,

                    'gross_sales' => number_format($_transaction->gross_sales, 2),
                    'cash_tendered' => number_format($_transaction->total_cash_tendered, 2),
                    'vatable_sales' => number_format($_transaction->vatable_sales, 2),
                    'change' => number_format($_transaction->change, 2),
                    'vat' => number_format($_transaction->vat, 2),
                    'vat_exempt_sales' => number_format($_transaction->vat_exempt_sales, 2),
                    'zero_rated_sales' => number_format($_transaction->zero_rated_sales, 2),
                    'total_sales' => number_format($_transaction->total_sales, 2),
                ];

                $basketItems = $_transaction->basket->items
                    // ->filter(fn($basketItem) => $basketItem->discount_value <= 0)
                    ->map(function ($basketItem) {
                        return [
                            'id' => $basketItem->item->id,
                            'type' => $basketItem->item->type,
                            'name' => $basketItem->item->type === 'product'
                                ? $basketItem->item->product?->name
                                : $basketItem->item->package?->name,
                            'quantity' => $basketItem->quantity,
                            'price' => $basketItem->item->type === 'product'
                                ? $basketItem->item->product?->price
                                : $basketItem->item->package?->price,
                        ];
                    })->values();

                $discountedBasketItems = $_transaction->basket->items
                    ->filter(fn($basketItem) => $basketItem->discount_value > 0)
                    ->map(function ($basketItem) {
                        return [
                            'id' => $basketItem->item->id,
                            'type' => $basketItem->item->type,
                            'name' => $basketItem->item->type === 'product'
                                ? $basketItem->item->product?->name
                                : $basketItem->item->package?->name,
                            'quantity' => $basketItem->quantity,
                            'price' => $basketItem->item->type === 'product'
                                ? $basketItem->total_value
                                : $basketItem->total_value,
                            'discount_value' => number_format($basketItem->discount_value, 2),
                        ];
                    })->values();

                    $_method_list = [];
                    foreach($_transaction->paymentMethods as $method){
                        array_push($_method_list, PaymentMethod::find($method->payment_method_id)->name);
                    }

                    //Create Journal List
                    $_journal_transaction_details = [
                        '  -----       REPRINT       ----  ',
                        'Date :' . str_pad(now()->format('F d, Y'), 28, ' ', STR_PAD_LEFT),
                        'Time :' . str_pad(now()->format('h:i A'), 28, ' ', STR_PAD_LEFT) . PHP_EOL,
                        '  -----    VOID INVOICE     ----  ',
                        '   Thermozone Philippines Corp.   ',
                        ' 2286 Marconi St., Brgy. San Isid ',
                        '        ro City of Makati,        ',
                        '  VAT REG TIN: 223-661-818-00000  ',
                        ' -------------------------------- ',
                        ' MIN: '.'XXXXXXXXXX   ',
                        ' Serial No : ' . 'XXXXXXXXXX   ',
                        ' ',
                        'Issued By : ' . auth()->user()->name,
                        'Invoice NO : ' . str_pad($_transaction->id, 12, '0', STR_PAD_LEFT),
                        'Date : ' . $_transaction->created_at->format('F d, Y'),
                        'Payment Method : ' . implode(', ' , $_method_list),
                        '----------------------------------',
                        ' -----    ITEM BREAKDOWN    ----- ',
                        ' Qty     Item     Price     Total ',
                    ];

                    $_journal_item_details = [];

                    foreach ($basketItems as $_basket_item) {
                        $_item = Item::find($_basket_item['id']);
                        if($_item->package){
                            $_item_data = Package::find($_item->package_id);
                        }
                        if($_item->product){
                            $_item_data = Product::find($_item->product_id);
                        }
                        array_push($_journal_item_details,
                            ' ' . $_basket_item['quantity'] .
                            '    ' . $_item_data->name .
                            '    -' . number_format($_item_data->price, 2) .
                            '    -' . number_format($_item_data->total_value, 2));
                    }
                    $_journal_sales_details = [
                        '----------------------------------',
                        'Cash Tendered : ' . str_pad("-".number_format($_transaction->total_cash_tendered ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                        'VATable Sales : ' . str_pad("-".number_format($_transaction->vatable_sales ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                        'Change : ' . str_pad("-".number_format($_transaction->change ?? 0, 2), 25, ' ', STR_PAD_LEFT),
                        'VAT : ' . str_pad("-".number_format($_transaction->vat ?? 0, 2), 28, ' ', STR_PAD_LEFT),
                        'VAT Exempt Sales : ' . str_pad("-".number_format($_transaction->vat_exempt_sales ?? 0, 2), 15, ' ', STR_PAD_LEFT),
                        'Zero Rated Sales : ' . str_pad("-".number_format($_transaction->zero_rated_sales ?? 0, 2), 15, ' ', STR_PAD_LEFT),
                        'Total Sales : ' . str_pad("-".number_format($_transaction->total_sales ?? 0, 2), 20, ' ', STR_PAD_LEFT),
                        '',
                        '           ' . str_pad($_transaction->id, 12, '0', STR_PAD_LEFT) . '     ',
                        '',
                        '   THERMOZONE PHILIPPINES CORP.   ',
                        '   2286 Marconi St. Makati City   ',
                        '  VAT REG TIN: 223-661-818-00000  ',
                        ' Accreditation Number: XXXXXXXXXX ',
                        '      ATG Number: XXXXXXXXXX      ' . PHP_EOL,
                    ];

                    $_journal_list = array_merge(
                        $_journal_transaction_details,
                        $_journal_item_details,
                        $_journal_sales_details
                    );

                    Journal::createJournalEntry(Carbon::now()->format('Ymd'), 'void_invoice_reprint',  $_journal_list);

                $response = [
                    'message' => 'Transaction voided successfully.',
                    'transaction_details' => $transactionDetails,
                    'items' => $basketItems,              // only items WITHOUT discounts
                    'discounted_items' => $discountedBasketItems, // only items WITH discounts
                ];

                return response()->json($response, 200);


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
                            'total' => $item['gross_income'],
                        ]);
                        return;
                    });
                    $data['products_excluded_in_package']->map(function ($item) use ($result){
                        $result->push([
                            'name' => $item['name'],
                            'price' =>$item['price'],
                            'qty' => $item['quantity'],
                            'total' => $item['gross_income'],
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
                                'total' => $item['gross_income'],
                            ]);
                            return;
                        });
                        $data['products_excluded_in_package']->map(function ($item) use ($result){
                            $result->push([
                                'name' => $item['name'],
                                'price' =>$item['price'],
                                'qty' => $item['quantity'],
                                'total' => $item['gross_income'],
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
                    $stub_no = $this->generateDailyCounter();

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

    function generateDailyCounter(): string
    {

        do {
            $latestStub = Stub::whereDate('created_at', Carbon::today())
                ->orderBy('stub_no', 'desc')
                ->first();

            if ($latestStub) {
                $newCount = intval($latestStub->stub_no) + 1;
            } else {
                $newCount = 1;
            }

            $stubNo = str_pad($newCount, 6, '0', STR_PAD_LEFT);

            $exists = Stub::whereDate('created_at', Carbon::today())
                ->where('stub_no', $stubNo)
                ->exists();

        } while ($exists);

        return $stubNo;
    }
    public function print($id) {
        try {
            $_transaction = Transaction::with([
                'basket.items.item.product',
                'basket.items.item.package'
            ])->find($id);

            if (!$_transaction) {
                return response()->json(['error' => 'Transaction not found.'], 404);
            }

            $_method_list = [];
            foreach ($_transaction->paymentMethods as $method){
                array_push($_method_list, PaymentMethod::find($method->payment_method_id)->name);
            }

            $transactionDetails = [
                'id' => $_transaction->id,
                'processed_by' => $_transaction->processedBy->name,
                'si_no' => str_pad($_transaction->id, 12, '0', STR_PAD_LEFT),
                'date' => $_transaction->created_at->format('F j, Y'),
                'time' => $_transaction->created_at->format('h:i A'),
                'payment_method' => implode(', ' , $_method_list),

                'is_sc' => $_transaction->is_sc,
                'is_pwd' => $_transaction->is_pwd,
                'is_nac' => $_transaction->is_nac,
                'is_soloparent' => $_transaction->is_soloparent,

                'gross_sales' => number_format($_transaction->gross_sales, 2),
                'cash_tendered' => number_format($_transaction->total_cash_tendered, 2),
                'vatable_sales' => number_format($_transaction->vatable_sales, 2),
                'change' => number_format($_transaction->change, 2),
                'vat' => number_format($_transaction->vat, 2),
                'vat_exempt_sales' => number_format($_transaction->vat_exempt_sales, 2),
                'zero_rated_sales' => number_format($_transaction->zero_rated_sales, 2),
                'total_sales' => number_format($_transaction->total_sales, 2),
            ];

            $basketItems = $_transaction->basket->items
                // ->filter(fn($basketItem) => $basketItem->discount_value <= 0)
                ->map(function ($basketItem) {
                    return [
                        'id' => $basketItem->item->id,
                        'type' => $basketItem->item->type,
                        'name' => $basketItem->item->type === 'product'
                            ? $basketItem->item->product?->name
                            : $basketItem->item->package?->name,
                        'quantity' => $basketItem->quantity,
                        'price' => $basketItem->item->type === 'product'
                            ? $basketItem->item->product?->price
                            : $basketItem->item->package?->price,
                    ];
                })->values();

            $discountedBasketItems = $_transaction->basket->items
                ->filter(fn($basketItem) => $basketItem->discount_value > 0)
                ->map(function ($basketItem) {
                    return [
                        'id' => $basketItem->item->id,
                        'type' => $basketItem->item->type,
                        'name' => $basketItem->item->type === 'product'
                            ? $basketItem->item->product?->name
                            : $basketItem->item->package?->name,
                        'quantity' => $basketItem->quantity,
                        'price' => $basketItem->item->type === 'product'
                            ? $basketItem->total_value
                            : $basketItem->total_value,
                        'discount_value' => number_format($basketItem->discount_value, 2),
                    ];
                })->values();

            //Create Journal List
            $_journal_transaction_details = [
                '  -----       REPRINT       ----  ',
                'Date :' . str_pad(now()->format('F d, Y'), 28, ' ', STR_PAD_LEFT),
                'Time :' . str_pad(now()->format('h:i A'), 28, ' ', STR_PAD_LEFT) . PHP_EOL,
                '  -----       INVOICE       ----  ',
                '   Thermozone Philippines Corp.   ',
                ' 2286 Marconi St., Brgy. San Isid ',
                '        ro City of Makati,        ',
                '  VAT REG TIN: 223-661-818-00000  ',
                ' -------------------------------- ',
                ' MIN: '.'XXXXXXXXXX   ',
                ' Serial No : ' . 'XXXXXXXXXX   ',
                ' ',
                'Issued By : ' . auth()->user()->name,
                'Invoice NO : ' . str_pad($_transaction->id, 12, '0', STR_PAD_LEFT),
                'Date : ' . $_transaction->created_at->format('F d, Y'),
                'Payment Method : ' . implode(', ' , $_method_list),
                '----------------------------------',
                ' -----    ITEM BREAKDOWN    ----- ',
                ' Qty     Item     Price     Total ',
            ];

            $_journal_item_details = [];
            $_discount_value = 0.0;
            foreach ($basketItems as $_basket_item) {
                $_item = Item::find($_basket_item['id']);
                if($_item->package){
                    $_item_data = Package::find($_item->package_id);
                }
                if($_item->product){
                    $_item_data = Product::find($_item->product_id);
                }
                array_push($_journal_item_details,
                    ' ' . $_basket_item['quantity'] .
                    '    ' . $_item_data->name .
                    '   @' . number_format($_item_data->price, 2) .
                    '    ' . number_format($_item_data->total_value, 2));
                $_discount_value += $_basket_item['discount_value'] ?? 0;
            }
            $_journal_sales_details = [
                '----------------------------------',
                'Cash Tendered : ' . str_pad(number_format($_transaction->total_cash_tendered ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                'VATable Sales : ' . str_pad(number_format($_transaction->vatable_sales ?? 0, 2), 18, ' ', STR_PAD_LEFT),
                'Change : ' . str_pad(number_format($_transaction->change ?? 0, 2), 25, ' ', STR_PAD_LEFT),
                'VAT : ' . str_pad(number_format($_transaction->vat ?? 0, 2), 28, ' ', STR_PAD_LEFT),
                'VAT Exempt Sales : ' . str_pad(number_format($_transaction->vat_exempt_sales ?? 0, 2), 15, ' ', STR_PAD_LEFT),
                'Zero Rated Sales : ' . str_pad(number_format($_transaction->zero_rated_sales ?? 0, 2), 15, ' ', STR_PAD_LEFT),
                'Amount Due :  ' . str_pad(number_format($_transaction->total_sales ?? 0, 2), 20, ' ', STR_PAD_LEFT),
                '',
                '           ' . str_pad($_transaction->id, 12, '0', STR_PAD_LEFT) . '     ',
                '',
                '   THERMOZONE PHILIPPINES CORP.   ',
                '   2286 Marconi St. Makati City   ',
                '  VAT REG TIN: 223-661-818-00000  ',
                ' Accreditation Number: XXXXXXXXXX ',
                '      ATG Number: XXXXXXXXXX      ' . PHP_EOL,
            ];

            $_journal_list = array_merge(
                $_journal_transaction_details,
                $_journal_item_details,
                $_journal_sales_details
            );

            Journal::createJournalEntry(Carbon::now()->format('Ymd'), 'invoice_reprint',  $_journal_list);

            $response = [
                'transaction_details' => $transactionDetails,
                'items' => $basketItems,              // only items WITHOUT discounts
                'discounted_items' => $discountedBasketItems, // only items WITH discounts
            ];



            return response()->json($response, 200);
        } catch (Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }
}

