<table>
    <thead>
        <tr>
            <th>Sr.No</th>
            <th>Employee Name</th>
            <th>Total Received</th>
            <th>Leads Generated</th>
            <th>Lead Given</th>
            <th>Lead Conveted Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['emp_name'] }}</td>
                <td>{{ $row['leads_generated'] }}</td>
                <td>{{ $row['leads_generated'] }}</td>
                <td>{{ $row['leads_assigned'] }}</td>
                <td>₹ {{ number_format($row['converted_amount']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
