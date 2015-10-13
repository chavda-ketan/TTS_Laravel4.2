@extends('main')

@section('content')

<div class="row">
    <h2>Mississauga</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Company</th>
                <th>AP Name</th>
                <th>AP Phone #</th>
                <th>AP Email</th>
                <th>Balance</th>
                <th>Credit Limit</th>
                <th>Last Visit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mississauga as $customer)
            <tr>
                <td>{{ $customer->Company }} <br> {{ $customer->AccountNumber }}</td>
                <td>{{ $customer->CustomText1 }}</td>
                <td>{{ $customer->CustomText2 }}</td>
                <td>{{ $customer->CustomText3 }}</td>
                <td>${{ number_format((float) $customer->AccountBalance, 2, '.', '') }}</td>
                <td>${{ number_format((float) $customer->CreditLimit, 2, '.', '') }}</td>
                <td>{{ $customer->LastVisit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="row">
    <h2>Toronto</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Company</th>
                <th>AP Name</th>
                <th>AP Phone #</th>
                <th>AP Email</th>
                <th>Balance</th>
                <th>Credit Limit</th>
                <th>Last Visit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($toronto as $customer)
            <tr>
                <td>{{ $customer->Company }}<br>{{ $customer->AccountNumber }}</td>
                <td>{{ $customer->CustomText1 }}</td>
                <td>{{ $customer->CustomText2 }}</td>
                <td>{{ $customer->CustomText3 }}</td>
                <td>${{ number_format((float) $customer->AccountBalance, 2, '.', '') }}</td>
                <td>${{ number_format((float) $customer->CreditLimit, 2, '.', '') }}</td>
                <td>{{ $customer->LastVisit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop

@section('scripts')

<script type="text/javascript">
    $(function(){
        $('.table').tablesorter();
    });
</script>

@stop
