@extends('main')

@section('content')
<div class="row">
    <h2>Transfer Details <a class="pull-right" href="/inventory">Return to List</a></h2>
    <h3>{{$transfer->title}} - {{ $transfer->sending }} to {{ $transfer->receiving }}
        <span class="pull-right"><b>Status</b>: @if($transfer->status == 0) In Progress @elseif($transfer->status == 1) Complete @else Cancelled @endif</span>
    </h3>
    <h4><b>Created</b>: {{ $transfer->created }}</h4>
    <h4><b>Last Updated</b>: {{ $transfer->lastupdated }}</h4>
    <hr>
</div>

<div class="row">
    <h4>Manifest
        @if($transfer->status == 0)
        <button id="complete" class="btn btn-success btn-sm pull-right" style="margin-top: -6px"> Complete Transfer </button>
        <a href="/inventory/details/{{ $transfer->id }}/cancel" id="cancel" class="btn btn-danger btn-sm pull-right" style="margin-top: -6px; margin-right: 10px"> Cancel Transfer </a>
        @elseif($transfer->status == 2)
        <a href="/inventory/details/{{ $transfer->id }}/reopen" class="btn btn-warning btn-sm pull-right" style="margin-top: -6px"> Re-open Transfer </a>
        @endif
    </h4>

    <table id="printable" class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th class="col-md-1">#</th>
                <th>SKU</th>
                <th>Item Name</th>
                <th class="sender col-md-1">Sent</th>
                <th class="receiver col-md-1">Received</th>
            </tr>
        </thead>

        <tbody id="transfer">
        @if($transfer->status == 0 || $transfer->status == 2)
            @foreach($manifest as $item)
                <tr>
                    <td>{{ $item->{'#'} }}</td>
                    <td>{{ $item->{'SKU'} }}</td>
                    <td>{{ $item->{'Item Name'} }}</td>
                    <td>{{ $item->{'Qty (Tfer)'} }}</td>
                    @if($transfer->status == 0)
                        <td><input type="number" min="0" class="form-control input-sm" value="{{ $item->{'Qty (Tfer)'} }}"></td>
                    @else
                    <td></td>
                    @endif
                </tr>
            @endforeach
        @else
            @foreach($finalmanifest as $item)
                <tr>
                    <td>{{ $item->{'#'} }}</td>
                    <td>{{ $item->{'SKU'} }}</td>
                    <td>{{ $item->{'Item Name'} }}</td>
                    <td>{{ $item->{'Sent'} }}</td>
                    <td>{{ $item->{'Received'} }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>

@stop



@section('scripts')

<script type="text/javascript">
$(function(){
    $("#complete").on('click', function() {
        var c = confirm("This will complete the transfer and adjust RMS inventory data.\n\nAre you sure?")

        if(c == true) {
            completeTransfer();
        }
    });
});

function completeTransfer(){
    $('td input').replaceWith(function(){
       return this.value;
    });

    var table = $('#printable').tableToJSON();

    var transfer = JSON.stringify(table);

    $.post( "/inventory/details/{{ $transfer->id }}/complete", {
        data: transfer
    }).done(function( data ) {
        window.location.reload();
    });
}

</script>

<style type="text/css">
    div.margin { margin: 10px -15px; }
    tr.no { display: none; }
    tr.yes { display: table-row; }
    @page{ size: auto; margin: 3mm; }
</style>
@stop
