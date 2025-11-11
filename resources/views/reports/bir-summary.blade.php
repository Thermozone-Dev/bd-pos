<section style="padding: 5px; font-family: Arial, sans-serif;">
    <div style="text-align: center; line-height: 0.55em;">
        <h1>Thermozone Philippines Corporation</h1>
        <p>2286 Marconi St. San Isidro Makati City</p>
        <p>VAT REG. TIN: 223-661-818-00000</p>
    </div><br>
    <div style="text-align: left; line-height: 0.55em;">
        <p>Software Name: <b>POS Sikat v1.0</b></p>
        <p>Serial No: <b>XXXXXXXXXX</b></p>
        <p>MIN: <b>XXXXXXXXXX</b></p>
        <p>POS Terminal Number: <b>XXX</b></p>
        <p>Date and Time Generated: <b>
            @php
                echo \Carbon\Carbon::now()->format('F j, Y h:i A');
            @endphp</b>
        </p>
        <p>Issued by: <b>{{ Auth::user()->name }}</b></p>
    </div><br>
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>BIR Sales Summary Report</b></caption>
        <thead>
            <tr>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Date</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Beginning Invoice No.</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Ending Invoice No.</th>
                <th rowspan="3" style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Grand Accum. Sales Ending Balance</th>
                <th rowspan="3" style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Grand Accum. Beg. Balance</th>
                <th rowspan="3" style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Sales Issued w/ Manual OR</th>
                <th rowspan="3" style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Gross Sales for the Day</th>
                <th rowspan="3" style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">VATable Sales</th>
                <th rowspan="3" style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">VAT Amount</th>
                <th rowspan="3" style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">VAT-Exempt Sales</th>
                <th rowspan="3" style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">Zero-Rated Sales</th>
                <th colspan="8" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Deductions</th>
                <th colspan="6" style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Adjustments on VAT</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">VAT Payable</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Net Sales</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Overflow</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Total Income</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Reset Counter</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Z-Counter</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Remarks</th>
            </tr>
            <tr>
                <th colspan="5" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Discounts</th>
                <th rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Returns</th>
                <th rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Voids</th>
                <th rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Total Deductions</th>
                <th colspan="3" style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Discounts</th>
                <th rowspan="2" style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">VAT on Returns</th>
                <th rowspan="2" style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Others</th>
                <th rowspan="2" style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Total VAT Adjustment</th>
            </tr>
            <tr>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">SC</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">PWD</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">NAAC</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Solo Parent</th>
                <th style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Others</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">SC</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">PWD</th>
                <th style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Others</th>
            </tr>
        </thead>
        @foreach ($data as $datum)
            <tr style="background-color: white;">
                <td style="border: 1px solid black; padding: 8px;">{{ $datum['Date'] }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ str_pad($datum['Beginning SI'], 12, '0', STR_PAD_LEFT) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ str_pad($datum['Ending SI'], 12, '0', STR_PAD_LEFT) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Accumulated End Bal'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Accumulated Beg Bal'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;"></td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Gross Sales'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Vatable Sales'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['VAT'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['VAT Exempt Sales'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Zero Rated Sales'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['SC Discounts'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['PWD Discounts'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['NAAC Discounts'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Solo Parent Discounts'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Other Discounts'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Returns'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Void'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Total Deductions'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['SC Adjustments'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['PWD Adjustments'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Reg Discount Adjustments'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['VAT on Return'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Other VAT Adjustments'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Total VAT Adjustments'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['VAT Payable'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['NET Sales'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Overflow'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Total Income'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ number_format($datum['Reset Counter'], 2) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $datum['Z Counter'] }}</td>
                <td style="border: 1px solid black; padding: 8px;"></td>
            </tr>
        @endforeach
    </table>
</section>
