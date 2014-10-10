@extends('main')

@section('content')

<table id='maillog' class='table table-condensed' style='margin-top: 20px'>
	<thead>
		<tr>
			<th>Location</th> <th>Work Order</th> <th>Read</th> <th>Google+</th> <th>Yelp</th>
		</tr>
	</thead>

	<tbody>
	@foreach ($customers as $customer)
		<tr @if(isset($customer->class)) class='{{ $customer->class }}'
		data-toggle='popover' title='{{ $customer->firstName }} {{ $customer->lastName }}' data-placement='top' data-trigger='hover' data-content='Email: {{ $customer->emailAddress }}' @endif>
			<td>{{ $customer->s }}</td>
			<td>{{ $customer->wo_id }}</td>
			<td>{{ $customer->email_open_date }}</td>
			<td>{{ $customer->google_click_date }}</td>
			<td>{{ $customer->yelp_click_date }}</td>
		</tr>
	@endforeach
	</tbody>

</table>

@stop



@section('scripts')

<script type="text/javascript">
$(function(){
	$('#maillog').tablesorter();
	$('.success').popover();
});
</script>

@stop
