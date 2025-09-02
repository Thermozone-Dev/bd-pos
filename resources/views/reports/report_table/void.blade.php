<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>Void Transactions Report</b></caption>
    <thead>
        <tr>
            <th colspan="4" rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Transaction ID</th>
            <th colspan="4" rowspan="2" style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Total Sales</th>
            <th colspan="4" rowspan="2" style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">Payment Method</th>
            <th colspan="4" rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Transaction Date</th>
            <th colspan="4" rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Voided Date</th>
    </thead>
    @foreach ($voidInfos as $voidInfo)
        <tr style="background-color: white;">
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ str_pad($voidInfo->transaction_id, 6, '0', STR_PAD_LEFT) }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ number_format($voidInfo->transaction->total_sales, 2) }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $voidInfo->transaction->paymentMethod->name }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($voidInfo->transaction->created_at)->format('F j, Y - h:i A') }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($voidInfo->created_at)->format('F j, Y - h:i A') }}</td>
        </tr>
    @endforeach
</table>
