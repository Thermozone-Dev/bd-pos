<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>Senior Citizen Sales Book / Report</b></caption>
    <thead>
        <tr>
            <th colspan="4" rowspan="2" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Transaction ID</th>
            <th colspan="4" rowspan="2" style="background-color: #08b4f4; border: 1px solid black; padding: 8px; text-align: center;">Inclusive</th>
            <th colspan="4" rowspan="2" style="background-color: #fffc04; border: 1px solid black; padding: 8px; text-align: center;">Quantity</th>
            <th colspan="4" rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Stub Number</th>
            <th colspan="4" rowspan="2" style="background-color: #ffc000; border: 1px solid black; padding: 8px; text-align: center;">Claimed</th>
            <th colspan="4" rowspan="2" style="background-color: #92d050; border: 1px solid black; padding: 8px; text-align: center;">Created By</th>
            <th colspan="4" rowspan="2" style="background-color: #ffd966; border: 1px solid black; padding: 8px; text-align: center;">Date & Time</th>
        </tr>
    </thead>
    @foreach ($stubs as $stub)
        <tr style= "background-color: white;">
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ str_pad($stub->transaction_id, 6, '0', STR_PAD_LEFT) }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $stub->packageInclusive->name }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $stub->quantity }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $stub->stub_no }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $stub->status ? 'Yes' : 'No' }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $stub->createdBy->name }}</td>
            <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($stub->created_at)->format('F j, Y h:i A') }}</td>
        </tr>
    @endforeach
</table>
