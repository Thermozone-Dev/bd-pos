<div style="padding: 5px; font-family: Arial, sans-serif;">
    @include('reports.report_table.header')
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <caption style="border: 1px solid black; width: 100%; padding: 1em 0; background-color: white; color: black;"><b>POS-Sikat Activity Log</b></caption>
        <thead>
            <tr>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Date & Time</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Activity ID</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">User ID</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">User</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align :center;">Event</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Subject Type</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Subject ID</th>
                <th rowspan="3" style="background-color: #a6a6a6; border: 1px solid black; padding: 8px; text-align: center;">Values</th>
            </tr>
        </thead>
        @foreach ($activities as $activity)
                <tr style="background-color: white;">
                    <td style="border: 1px solid black; padding: 8px;">{{ \Carbon\Carbon::parse($activity['created_at'])->format('F j, Y h:i A') }} </td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $activity['id'] }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $activity['causer_id'] }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $activity['causer'] }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $activity['event'] }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $activity['subject_type'] }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $activity['subject_id'] }}</td>
                    <td style="border: 1px solid black; padding: 8px; word-wrap: break-word; max-width: 350px">{{ $activity['properties'] }}</td>
                </tr>
        @endforeach
    </table>
</div>
