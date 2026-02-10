<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>Senior Citizen Sales Book / Report</b></caption>
    <thead>
        <tr>
            <th colspan="4" rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Date</th>
            <th colspan="4" rowspan="2" style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Name of Senior Citizen</th>
            <th colspan="4" rowspan="2" style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">OSCA ID Number</th>
            <th colspan="4" rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">SC TIN</th>
            <th colspan="4" rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">SI / OR Number</th>
            <th colspan="4" rowspan="2" style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Sales (Inclusive of VAT)</th>
            <th colspan="4" rowspan="2" style="background-color: #ffd966; border: 1px solid black; padding: 8px; text-align: center;">VAT Amount</th>
            <th colspan="4" rowspan="2" style="background-color: #ffd966; border: 1px solid black; padding: 8px; text-align: center;">VAT Exempt Sales</th>
            <th colspan="8" rowspan="1" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Discount</th>
            <th colspan="4" rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Net Sales</th>
        </tr>
        <tr>
            <th colspan="4" rowspan="1" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">5%</th>
            <th colspan="4" rowspan="1" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">20%</th>
        </tr>
    </thead>
    @foreach ($scInfos as $scInfo)
        @php
            $scTransaction = $scTransactions[$scInfo->transaction_id] ?? null;
        @endphp
        @foreach ($transactionDiscounts as $key => $transactionDiscount)
            @if ($scTransaction->transaction_basket_id == $key)
                <tr style="background-color: white;">
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($scInfo->created_at)->format('F j, Y') }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $scInfo->name }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $scInfo->sc_id }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $scInfo->sc_tin }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ str_pad($scInfo->transaction_id, 12, '0', STR_PAD_LEFT) }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($scTransaction->vat_exempt_sales * 1.12, 2) }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($scTransaction->vat_adjustment, 2) }}</td>
                    {{-- <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($scTransaction->vat_exempt_sales, 2) }}</td> --}}
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($scTransaction->vat_exempt_sales, 2) }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px; text-align: center;">0</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($transactionDiscount, 2) }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($scTransaction->total_sales - $scTransaction->vat, 2) }}</td>
                </tr>
             @endif
        @endforeach
    @endforeach
</table>
