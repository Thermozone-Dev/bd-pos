<section style="padding: 5px; font-family: Arial, sans-serif;">
    <div style="text-align: center; line-height: 0.55em;">
        <h1>Thermozone Philippines Corporation</h1>
        <p>2280 Marconi St., Barangay San Isidro, Makati City</p>
        <p>TIN: 223 661 818 0000</p>
    </div><br>
    <div style="text-align: left; line-height: 0.55em;">
        <p>Software Name: <b>POS Sikat v1.0</b></p>
        <p>Serial Number: <b>XXXXXXXXXX</b></p>
        <p>Machine Identification Number: <b>XXXXXXXXXX</b></p>
        <p>POS Terminal Number: <b>XXX</b></p>

        <p>Date & Time Generated: <b>
            @php
                echo \Carbon\Carbon::now()->format('F j, Y h:i A');
            @endphp</b>
        </p>
        <p>Issued by: <b>{{ Auth::user()->name }}</b></p>
    </div><br>
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>General Transaction Summary Report</b></caption>
        <thead>
            <tr>
                <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Transaction ID</th>
                <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Processed By</th>
                <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Date</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Cash Tendered</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Transaction Fee</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Vatable Sales</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">VAT</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">VAT Exempt Sales</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Change</th>
                <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Zero Rated Sales</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">PWD</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Senior Citizen</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">National Athletes and Coaches</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Solo Parent</th>
                <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Total Sales</th>
                <th style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Gross Sales</th>
            </tr>
        </thead>
        @foreach ($transactions as $transaction)
            <tr style="background-color: white;">
                <td style="border: 1px solid black; padding: 8px;">{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $transaction->processed_by }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ date_format($transaction->created_at, 'M d, Y') }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->cash_tendered, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->transaction_fee, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->vatable_sales, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->vat, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->vat_exempt_sales, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->change, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->zero_rated_sales, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $transaction->is_pwd }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $transaction->is_sc }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $transaction->is_nac }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $transaction->is_soloparent }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->total_sales, 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($transaction->gross_sales, 2) }}</td>
            </tr>
        @endforeach
    </table>
</section>
