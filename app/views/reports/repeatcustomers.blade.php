@extends('main')

@section('notifications')
@stop

@section('content')
    <div class="row" style="margin-top:20px;">

        <div class='col-sm-6'>
			{{ $result }}
		</div>

		@if(isset($showpicker))
	        <div class='col-sm-4'>
		        <form role="form" action="repeats" id="datepicker-form">
		            <div class="form-group">
		                <div class='input-group date' id='datepicker'>
		                    <input type='text' name='d' class="form-control" id='date' value='{{ Input::get("m") }} {{ Input::get("y") }}'>
		                    <span class="input-group-addon">
		                    	<span class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div>
		            </div>
		            <input type="hidden" name="m" id="date-m">
		            <input type="hidden" name="y" id="date-y">
		            <button type="submit" class="btn btn-default">Submit</button>
		        </form>
	        </div>
	    @endif

	    <div class='col-sm-10'>
	    	<div id='graph' style="width: 100%; height: 500px;">

	    	</div>
	    </div>

	</div>

	<script type="text/javascript">
		var dates = [ {{ $dates }} ];
		var totals = [ {{ $total }} ];
		var repeats = [ {{ $repeat }} ];
		var referrals = [ {{ $referral }} ];
	</script>
@stop

@section('scripts')


<script type="text/javascript">
    $(function () {
		$('#datepicker input').datepicker({
		    format: 'MM yyyy',
		    minViewMode: 1,
		    // todayHighlight: true
		});

		$('#datepicker-form').submit(function() {
		    var data = $('#date').val();

		    var split = data.split(' ');

		    $('#date-m').val(split[0]);
		    $('#date-y').val(split[1]);
		    $('#date').val('');
		});


		$.plot("#graph", [ dates ], {
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: "center"
				}
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			}
		});
    });
</script>

@stop