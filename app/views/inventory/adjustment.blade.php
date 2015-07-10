@extends('main')

@section('content')
<div class="row">
    <h2>Log Inventory Adjustment</h2>
    <hr>
</div>


<div class="row">
    <table class="table table-bordered">
          <thead>
            <tr>
              <th>Date</th>
              <th>Location</th>
              <th>SKU</th>
              <th class="col-md-1">From</th>
              <th class="col-md-1">To</th>
              <th>Reason</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->timestamp }}</td>
                    <td>{{ $log->location }}</td>
                    <td>{{ $log->sku }}</td>
                    <td>{{ $log->from }}</td>
                    <td>{{ $log->to }}</td>
                    <td>{{ $log->reason }}</td>
                </tr>
            @endforeach
          </tbody>
    </table>
</div>


@stop



@section('scripts')

@stop