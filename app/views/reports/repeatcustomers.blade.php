@extends('main')

@section('notifications')
@stop

@section('content')
    <div class="row">

        <div class='col-sm-6'>
	        <form role="form" action="repeats">
	            <div class="form-group">
	                <div class='input-group date' id='datepicker'>
	                    <input type='text' name='d' class="form-control">
	                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
	            </div>
	            <button type="submit" class="btn btn-default">Submit</button>
	        </form>
        </div>

        <div class='col-sm-12'>
			{{ $result }}
		</div>
	</div>
@stop

@section('scripts')

<script type="text/javascript">
    $(function () {
		$('#datepicker input').datepicker({
		    format: "MM yyyy",
		    minViewMode: 1,
		    // todayHighlight: true
		});
    });
</script>

@stop