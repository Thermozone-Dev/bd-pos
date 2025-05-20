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

        <p>Date & Time Generated: <b>XXXXXXXXX</b></p>
        <p>Issued by: <b>{{ Auth::user()->name }}</b></p>
    </div><br>
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>General Transaction Summary Report</b></caption>
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Transaction ID</th>
                <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Processed By</th>
                <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Created At</th>


                <th colspan="6" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Values</th>

                <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Zero Rated Sales</th>

                <th colspan="4" style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Discounts</th>

                <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Total Sales</th>
                <th rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Gross Sales</th>
            </tr>
            <tr>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Cash Tendered</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Transaction Fee</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Vatable Sales</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Vat</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Vat Exempt Sales</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Change</th>


                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">PWD</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Senior Citizen</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">National Athletes and Coaches</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Solo Parent</th>
            </tr>
        </thead>
        @foreach ($transactions as $transaction)

            <tr style="background-color: white;">
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->id }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->processed_by }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->created_at }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->zero_rated_sales }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->total_sales }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->gross_sales }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->cash_tendered }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->transaction_fee }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->vatable_sales }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->vat }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->vat_exempt_sales }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->change }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->is_pwd }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->is_sc }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->is_nac }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test{{ $transaction->is_soloparent }}</td>
            </tr>
        @endforeach
    </table>
</section>
