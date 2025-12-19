<table>
    <thead>
        <tr>
            <th>Sr.No</th>
            <th>Leads Received</th>
            <th>Pipeline</th>
            <th>Count</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->lead_source_name }}</td>
                <td>{{ $row->pipeline_name }}</td>
                <td>{{ $row->lead_count }}</td>
                <td>₹ {{ number_format($row->total_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
