<table class="table table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>WO#</th>
            <th>Device</th>
            <th>Date</th>
            <th>Billing</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>
        <?php $count = 1; ?>
        @foreach($workorders['squareone'] as $workorder)
        <tr>
            <td>{{ $count++ }}</td>
            <td>{{ $workorder->OrderID }}</td>
            <td>{{ $workorder->Description }}</td>
            <td>{{ date("m/d/Y", strtotime($workorder->Time)) }}</td>
            <td>${{ number_format((float) $workorder->Price, 2, '.', '') }}</td>
            <td>Square One</td>
        </tr>
        @endforeach

        @foreach($workorders['toronto'] as $workorder)
        <tr>
            <td>{{ $count++ }}</td>
            <td>{{ $workorder->OrderID }}</td>
            <td>{{ $workorder->Description }}</td>
            <td>{{ date("m/d/Y", strtotime($workorder->Time)) }}</td>
            <td>${{ number_format((float) $workorder->Price, 2, '.', '') }}</td>
            <td>Toronto</td>
        </tr>
        @endforeach
    </tbody>
</table>