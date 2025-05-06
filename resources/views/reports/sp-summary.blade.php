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
            @php
                $spTransaction = $spTransactions[$spInfo->transaction_id] ?? null;
            @endphp
            <tr style="background-color: white;">
                <td style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($spInfo->created_at)->format('F j, Y') }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $spInfo->name }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $spInfo->spic_id }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $spInfo->child_name }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($spInfo->child_birthday)->format('F j, Y') }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $spInfo->child_age }}</td>
                <td style="border: 1px solid black; padding: 8px;">Test</td>
                <td style="border: 1px solid black; padding: 8px;">{{ str_pad($spInfo->transaction_id, 6, '0', STR_PAD_LEFT) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $spTransaction->gross_sales }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $spTransaction->total_sales }}</td>
            </tr>
        @endforeach

    </table>
</section>
