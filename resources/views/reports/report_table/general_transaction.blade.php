<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;">
        <b>General Transaction Summary Report</b>
    </caption>
    <thead>
        <!-- First row: main group headers -->
        <tr>
            <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Transaction ID</th>
            <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Processed By</th>
            <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Date</th>
            <th colspan="6" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Financials</th>
            <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Zero Rated Sales</th>
            <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Total Sales</th>
            <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Gross Sales</th>
        </tr>
        <!-- Second row: sub-headers for grouped columns -->
        <tr>
            <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Cash Tendered</th>
            <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Transaction Fee</th>
            <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Vatable Sales</th>
            <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">VAT</th>
            <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">VAT Exempt Sales</th>
            <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Change</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $transaction)
            <tr style="background-color: white;">
                <td style="border: 1px solid black; padding: 8px;">{{ str_pad($transaction->id, 12, '0', STR_PAD_LEFT) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $transaction->processedBy()->first()->name }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ date_format($transaction->created_at, 'm/d/Y') }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->total_cash_tendered, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->transaction_fee, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->vatable_sales, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->vat, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->vat_exempt_sales, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->change, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->zero_rated_sales, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->total_sales, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->gross_sales, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
