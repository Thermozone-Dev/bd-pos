<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>Solo Parent Sales Book / Report</b></caption>
    <thead>
        <tr>
            <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Date</th>
            <th style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Name of Solo Parent</th>
            <th style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">SPIC Number</th>
            <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Name of Child</th>
            <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Birthdate of Child</th>
            <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Age of Child</th>
            <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Discount</th>
            <th style="background-color: #ffd966; border: 1px solid black; padding: 8px; text-align: center;">SI / OR Number</th>
            <th style="background-color: #ffd966; border: 1px solid black; padding: 8px; text-align: center;">Gross Sales</th>
            <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Net Sales</th>
        </tr>
    </thead>
    @foreach ($spInfos as $spInfo)
        @foreach ($spTransactions as $spTransaction)
            @if ($spInfo->transaction_id == $spTransaction->id)
                @foreach ($transactionDiscounts as $key => $transactionDiscount)
                    @if ($spTransaction->transaction_basket_id == $key)
                        <tr style="background-color: white;">
                            <td style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($spInfo->created_at)->format('F j, Y') }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ $spInfo->name }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ $spInfo->spic_id }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ $spInfo->child_name }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($spInfo->child_birthday)->format('F j, Y') }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ $spInfo->child_age }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ number_format($transactionDiscount, 2) }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ str_pad($spInfo->transaction_id, 12, '0', STR_PAD_LEFT) }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ number_format($spTransaction->gross_sales, 2) }}</td>
                            <td style="border: 1px solid black; padding: 8px;">{{ number_format($spTransaction->total_sales, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
        @endforeach
    @endforeach

</table>
