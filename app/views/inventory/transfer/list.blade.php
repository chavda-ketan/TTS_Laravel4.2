@extends('main')

@section('content')
<div class="row">
    <h2>Inventory Transfers
        <a class="btn btn-success btn-lg pull-right" href="/inventory/create"> Create Transfer </a>
    </h2>
    <hr>
</div>


<div class="row">
    <table class="table table-bordered table-hover table-condensed">
          <thead>
            <tr>
              <th class="col-sm-2">Created</th>
              <th>Name</th>
              <th class="col-sm-1">From</th>
              <th class="col-sm-1">To</th>
              <th class="col-sm-1">Status</th>
              <th class="col-sm-2">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($transfers as $transfer)
                @if($transfer->status == 0)
                    <tr class="warning">
                @elseif($transfer->status == 1)
                    <tr class="success">
                @else
                    <tr class="danger">
                @endif

                    <td>{{ $transfer->created }}</td>
                    <td>{{ $transfer->title }}</td>
                    <td>{{ $transfer->sending }}</td>
                    <td>{{ $transfer->receiving }}</td>

                    @if($transfer->status == 0)
                        <td>In Progress</td>
                    @elseif($transfer->status == 1)
                        <td>Completed</td>
                    @else
                        <td>Cancelled</td>
                    @endif

                    <td class="col-sm-1">
                    @if($transfer->status == 0)
                        <a href="/inventory/details/{{ $transfer->id }}/edit" class="btn btn-warning btn-sm details"> Edit </a>
                        <a href="/inventory/details/{{ $transfer->id }}" class="btn btn-primary btn-sm details"> Accept </a>
                    @else
                        <a href="/inventory/details/{{ $transfer->id }}" class="btn btn-default btn-sm details"> View </a>
                    @endif

                    </td>
                </tr>
            @endforeach
          </tbody>
    </table>
</div>
@stop


@section('scripts')
@stop