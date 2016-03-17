@extends('main')

@section('content')

<div class="row">
    <h2>Credit Customers</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Company</th>
                <th>Email</th>
                <th>AP Name</th>
                <th>AP Phone #</th>
                <th>AP Email</th>
                <th>Balance</th>
                <th>Total Sales</th>
                <th>Credit Limit</th>
                <th>Last Visit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td><a class="modalLink" data-company="{{ $customer->Company }}" href="#">{{ $customer->Company }} <br> {{ $customer->AccountNumber }}</a></td>
                <td>{{ $customer->EmailAddress }}</td>
                <td>{{ $customer->CustomText1 }}</td>
                <td>{{ $customer->CustomText2 }}</td>
                <td>{{ $customer->CustomText3 }}</td>
                <td>${{ number_format((float) $customer->AccountBalance, 2, '.', '') }}</td>
                <td>${{ number_format((float) $customer->TotalSales, 2, '.', '') }}</td>
                <td>${{ number_format((float) $customer->CreditLimit, 2, '.', '') }}</td>
                <td>{{ $customer->LastVisit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Work Order History - <span id="extraData"></span></h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@stop

@section('scripts')

<script type="text/javascript">
    $(function(){
        $('.table').tablesorter();

        $('a.modalLink').on('click', function(){
            var company = $(this).data('company');
            $("#extraData").text(company);
            $( ".modal-body" ).load( "/statement/credithistory", { company: company }, function() {
              $('#myModal').modal();
            });
        });
    });
</script>

@stop
