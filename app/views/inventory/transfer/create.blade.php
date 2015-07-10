@extends('main')

@section('content')
<div class="row">
    <h2>Create Transfer</h2>
    <hr>
</div>

<form id="search" class="" role="form">
    <div class="row form-inline margin">
        <div class="form-group">
            <label for="transfername" class="">Transfer Name</label>
            <input type="text" class="form-control input-sm" id="transfername" placeholder="Transfer Name">
        </div>
    </div>

    <div class="row form-inline margin">
        <div class="form-group">
            <label for="from">Send From</label>
            <select type="select" class="form-control input-sm destination" id="from">
                <option value="Mississauga">Mississauga</option>
                <option value="Toronto">Toronto</option>
            </select>

            <label for="to">to</label>
            <select type="select" class="form-control input-sm destination" id="to">
                <option value="Toronto">Toronto</option>
                <option value="Mississauga">Mississauga</option>
            </select>
        </div>
    </div>

    <div class="row form-inline">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3 class="panel-title">Filters</h3>
            </div>

            <div class="panel-body">
                <div style="margin-bottom: 10px">
                Saved Filters:
                    <button type="button" id="laptop" style="" class="btn btn-default btn-sm ">Laptop Batteries</button>
                    <button type="button" id="adapters" style="" class="btn btn-default btn-sm ">Laptop Adapters</button>
                    <button type="button" id="phone" style="" class="btn btn-default btn-sm ">Phone Batteries</button>
                </div>

                <hr>

                <div class="form-inline">
                    <input type="text" class="form-control input-sm" id="sku" placeholder="Item Name/SKU">

                    <label for="to">Department</label>
                    <select type="select" class="form-control input-sm" id="department">
                        <option></option>
                        <option data-id="all">All</option>
                        @foreach ($departments as $department)
                            <option data-id='{{ $department->ID }}'>{{ $department->Name }}</option>
                        @endforeach
                    </select>

                    <label for="to">Category</label>
                    <select type="select" class="form-control input-sm" id="category">
                        <option></option>
                        <option data-id="all">All</option>
                    </select>


                    <button type="submit" id="search" class="btn btn-primary btn-sm">Search</button>
                </div>
            </div>
        </div>

    </div>
</form>

<div class="row">
    <h4>Search
        <button id="addall" style="margin-top: -6px" class="btn btn-default btn-sm pull-right">Add All</button>

        <div class="btn-group pull-right" style="margin-top: -6px; margin-right: 10px;" role="group">
            <button type="button" id="showsuggested" class="btn btn-sm btn-default active">Show Suggested</button>
            <button type="button" id="showall" class="btn btn-sm btn-default">Show All</button>
        </div>
    </h4>

    <table class="table table-bordered" id="searchtable">
          <thead>
            <tr>
              <th class="col-md-1">#</th>
              <th>SKU</th>
              <th>Item Name</th>
              <th class="sender col-md-1">Qty (S)</th>
              <th class="receiver col-md-1">Qty (R)</th>
              <th class="col-md-1">Qty (Tfer)</th>
              <th class="col-md-1">Action</th>
            </tr>
          </thead>
          <tbody id="results">
          </tbody>
    </table>
</div>

<div class="row">
    <h4>Current Transfer
        <button id="savebtn" class="btn btn-success btn-sm pull-right" style="margin-top: -6px"> Save </button>
        <button id="printbtn" class="btn btn-primary btn-sm pull-right" style="margin-top: -6px; margin-right: 10px"> Print </button>
        <button id="clearbtn" class="btn btn-danger btn-sm pull-right" style="margin-top: -6px; margin-right: 10px"> Clear </button>
    </h4>

    <table id="printable" class="table table-bordered">
          <thead>
            <tr>
              <th class="col-md-1">#</th>
              <th>SKU</th>
              <th>Item Name</th>
              <th class="sender col-md-1">Qty (S)</th>
              <th class="receiver col-md-1">Qty (R)</th>
              <th class="col-md-1">Qty (Tfer)</th>
              <th class="noprint col-md-1">Action</th>
            </tr>
          </thead>
          <tbody id="transfer">
          </tbody>
    </table>
</div>

@stop



@section('scripts')

<script type="text/javascript">
$(function(){

    $.ajaxSetup({
        async: false
    });

    $("select#department").change(function(){
        $.getJSON("/inventory/department", {
            id: $(this).find(':selected').data('id')
        }, function(j){
            var options = '';
            options += '<option></option>';
            options += '<option data-id="all">All</option>';
            for (var i = 0; i < j.length; i++) {
                options += '<option data-id="' + j[i].ID + '">' + j[i].Name + '</option>';
            }
            $("select#category").html(options);
        });
    });

    $("#search").submit(function(event){
        $.getJSON("/inventory/search", {
            sku: $("input#sku").val(),
            department: $("select#department").find(':selected').data('id'),
            category: $("select#category").find(':selected').data('id'),
            from: $("select#from").find(':selected').attr('value')
        }, function(j){
            var results = '';
            num = 0;

            for (var i in j) {
                num++;
                results += '<tr data-sku="' + j[i].ItemLookupCode + '" class="' + j[i].SuggestedStatus + '">'
                results += '<td>' + num + '</td>'
                results += '<td>' + j[i].ItemLookupCode + '</td>'
                results += '<td>' + j[i].Description + '</td>'
                results += '<td>' + j[i].SendQuantity + '</td>'
                results += '<td>' + j[i].ReceiveQuantity + '</td>'
                results += '<td><input type="number" min="0" max="' + j[i].SendQuantity + '" class="form-control input-sm" id="' + j[i].ItemLookupCode + '" value="' + j[i].Recommended + '"></td>'
                results += '<td><button data-sku="' + j[i].ItemLookupCode + '" id="add' + j[i].ItemLookupCode + '" class="addbutton btn btn-success btn-sm">Add</button></td>'
                results += '</tr>'
            }

            $("tbody#results").html(results);
            $('#showall').removeClass('active');
            $('#showsuggested').addClass('active');
        });

        event.preventDefault();
    });

    $("#results").on('click', '.addbutton', function() {
        var sku = $(this).data('sku');
        //$('#'+sku)[0].checkValidity();

        var array = [];
        var headers = [];
        var row = '';

        $('#searchtable th').each(function(index, item) {
            headers[index] = $(item).text();
        });

        $(this).parent().parent().has('td').each(function() {
            var arrayItem = {};
            $('td', $(this)).each(function(index, item) {
                if (headers[index] === "Qty (Tfer)") {
                    arrayItem[headers[index]] = $(item).children('input').val();
                } else {
                    arrayItem[headers[index]] = $(item).text();
                };
            });
            array.push(arrayItem);
        });

        var sender = $("select#from").find(':selected').attr('value');
        var receiver = $("select#to").find(':selected').attr('value');
        var sendkey = "Qty ("+sender+")";
        var recvkey = "Qty ("+receiver+")";

        row += '<tr>'
        row += '<td>' + 0 + '</td>'
        row += '<td>' + array[0]["SKU"] + '</td>'
        row += '<td>' + array[0]["Item Name"] + '</td>'
        row += '<td>' + array[0][sendkey] + '</td>'
        row += '<td>' + array[0][recvkey] + '</td>'
        row += '<td>' + array[0]["Qty (Tfer)"] + '</td>'
        row += '<td class="noprint"><button class="delbutton btn btn-danger btn-sm" data-sku="' + array[0]["SKU"] + '">Del</button></td>'
        row += '</tr>'

        $('#transfer').append(row);
        $(this).hide();
        transferTableIndex();
    });

    $("#addall").on('click', function(){
        $('#results tr.undefined').each( function() {
            var sku = $(this).data('sku');
            console.log(sku);

            var array = [];
            var headers = [];
            var row = '';

            $('#searchtable th').each(function(index, item) {
                headers[index] = $(item).text();
            });

            $(this).has('td').each(function() {
                var arrayItem = {};
                $('td', $(this)).each(function(index, item) {
                    if (headers[index] === "Qty (Tfer)") {
                        arrayItem[headers[index]] = $(item).children('input').val();
                    } else {
                        arrayItem[headers[index]] = $(item).text();
                    };
                });
                array.push(arrayItem);
            });

            var sender = $("select#from").find(':selected').attr('value');
            var receiver = $("select#to").find(':selected').attr('value');
            var sendkey = "Qty ("+sender+")";
            var recvkey = "Qty ("+receiver+")";

            row += '<tr>'
            row += '<td>' + 0 + '</td>'
            row += '<td>' + array[0]["SKU"] + '</td>'
            row += '<td>' + array[0]["Item Name"] + '</td>'
            row += '<td>' + array[0][sendkey] + '</td>'
            row += '<td>' + array[0][recvkey] + '</td>'
            row += '<td>' + array[0]["Qty (Tfer)"] + '</td>'
            row += '<td class="noprint"><button class="delbutton btn btn-danger btn-sm" data-sku="' + array[0]["SKU"] + '">Del</button></td>'
            row += '</tr>'

            $('#transfer').append(row);

        });
    });

    $("#transfer").on('click', '.delbutton', function() {
        var sku = $(this).data('sku');
        $('#add'+sku).show();
        $(this).parent().parent().remove();
        transferTableIndex();
    });

    $('#clearbtn').on('click', function() {
        $('#transfer').empty();
        $('.addbutton').show();
    });

    $("button#printbtn").on('click', function() {
        $("#printable").print({
            noPrintSelector : ".noprint",
            prepend : "<h3>Inventory Transfer</h3><h4>" + $('#from').val() + " &#9654; " + $('#to').val() + "</h4><hr>",
        });
    });

    $("#showsuggested").on('click', function() {
        $('#results tr').removeClass('yes');
        $('#showall').removeClass('active');
        $('#showsuggested').addClass('active');
    });

    $("#showall").on('click', function() {
        $('#results tr').addClass('yes');
        $('#showsuggested').removeClass('active');
        $('#showall').addClass('active');
    });


    $("#transfer").bind("sortEnd", transferTableIndex());

    $("select.destination").change(function () {
        setTableHeaders();
    });

    setTableHeaders();

    $("#savebtn").on('click', function() {
        saveTransfer();
    });

    $("#laptop").on('click', function() {
        $('#department').val('All').change();
        $('#category').val('Laptop Battery');
        $('#search').submit();
    });

    $("#phone").click(function() {
        $('#department').val('All').change()
        $('#category').val('Phone Battery');
        $('#search').submit();
    });

    $("#adapters").on('click', function() {
        $('#department').val('Adapters - Laptop').change();
        $('#category').val('Generic');
        $('#search').submit();
    });

});

function addTransferRows(){
            $('#results tr:not(.no)').each( function() {
            var sku = $(this).data('sku');
            //$('#'+sku)[0].checkValidity();

            var array = [];
            var headers = [];
            var row = '';

            $('#searchtable th').each(function(index, item) {
                headers[index] = $(item).text();
            });

            $(this).parent().parent().has('td').each(function() {
                var arrayItem = {};
                $('td', $(this)).each(function(index, item) {
                    if (headers[index] === "Qty (Tfer)") {
                        arrayItem[headers[index]] = $(item).children('input').val();
                    } else {
                        arrayItem[headers[index]] = $(item).text();
                    };
                });
                array.push(arrayItem);
            });

            var sender = $("select#from").find(':selected').attr('value');
            var receiver = $("select#to").find(':selected').attr('value');
            var sendkey = "Qty ("+sender+")";
            var recvkey = "Qty ("+receiver+")";

            row += '<tr>'
            row += '<td>' + 0 + '</td>'
            row += '<td>' + array[0]["SKU"] + '</td>'
            row += '<td>' + array[0]["Item Name"] + '</td>'
            row += '<td>' + array[0][sendkey] + '</td>'
            row += '<td>' + array[0][recvkey] + '</td>'
            row += '<td>' + array[0]["Qty (Tfer)"] + '</td>'
            row += '<td class="noprint"><button class="delbutton btn btn-danger btn-sm" data-sku="' + array[0]["SKU"] + '">Del</button></td>'
            row += '</tr>'

            $('#transfer').append(row);

        });

}

function setTableHeaders(){
    var sender = $("select#from").find(':selected').attr('value');
    var receiver = $("select#to").find(':selected').attr('value');
    var sendkey = "Qty ("+sender+")";
    var recvkey = "Qty ("+receiver+")";
    $('th.sender').text(sendkey);
    $('th.receiver').text(recvkey);
}

function transferTableIndex(){
    var i = 1;
    $("#transfer").find("tr").each(function(){
        $(this).find("td:eq(0)").text(i);
        i++;
    });
};

function saveTransfer(){
    var title = $("input#transfername").val();
    var sender = $("select#from").find(':selected').attr('value');
    var receiver = $("select#to").find(':selected').attr('value');

    var table = $('#printable').tableToJSON({
        ignoreColumns: [6]
    });

    var transfer = JSON.stringify(table);

    $.post( "/inventory/save", {
        title: title,
        sending: sender,
        receiving: receiver,
        data: transfer
    }).done(function( data ) {
        window.location.href = '/inventory';
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
