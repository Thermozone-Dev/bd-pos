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
        <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>Persons with Disability Sales Book / Report</b></caption>
        <thead>
            <tr>
                <th colspan="4" rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Date</th>
                <th colspan="4" rowspan="2" style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Name of Person with Disability</th>
                <th colspan="4" rowspan="2" style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">PWD ID Number</th>
                <th colspan="4" rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">PWD TIN</th>
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
        @foreach ($pwdInfos as $pwdInfo)
            @php
                $pwdTransaction = $pwdTransactions[$pwdInfo->transaction_id] ?? null;
            @endphp
            <tr style="background-color: white;">
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($pwdInfo->created_at)->format('F j, Y') }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $pwdInfo->name }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $pwdInfo->pwd_id }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $pwdInfo->pwd_tin }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ str_pad($pwdInfo->transaction_id, 6, '0', STR_PAD_LEFT) }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($pwdTransaction->gross_sales, 2) }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($pwdTransaction->vat, 2) }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($pwdTransaction->vat_exempt, 2) }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px; text-align: center;">&#x2715;</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px; text-align: center;">&#x2713;</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px;">{{  number_format($pwdTransaction->total_sales, 2) }}</td>
            </tr>
        @endforeach
    </table>
</section>
