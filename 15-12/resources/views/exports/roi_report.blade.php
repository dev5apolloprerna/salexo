<table>
    <thead>
        <tr>
            <th>Sr.No</th>
            <th>Lead Source</th>
            <th>Leads found</th>
            <th>Leads Converted</th>
            <th>Lead Converted Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data['source_name'] }}</td>
                <td>{{ $data['leads_found'] }}</td>
                <td>{{ $data['leads_converted'] }}</td>
                <td>₹ {{ number_format($data['converted_amount']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
