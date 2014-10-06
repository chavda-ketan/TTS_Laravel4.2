@extends('main')

@section('notifications')
@stop

@section('content')
<style type="text/css">
	#outputsummary p {
	}
</style>


    <div class="row" style="margin-top:20px;">

		@if(isset($showsummary))
	        <div id="outputsummary" class='col-sm-6'>
	        	<div class='well'>

					<p>SquareOne total customers - {{ $mississaugaTotalCount }}</p>
					<p>SquareOne repeat customers - {{ $mississaugaRepeatCount }} - {{ $mississaugaPercentage }}% repeats</p>
					<p>SquareOne referral customers - {{ $mississaugaReferralCount }} - {{ $mississaugaReferralPercentage }}% referrals</p>

					<p>Toronto total customers - {{ $torontoTotalCount }}</p>
					<p>Toronto repeat customers - {{ $torontoRepeatCount }} - {{ $torontoPercentage }}% repeats</p>
					<p>Toronto referral customers - {{ $torontoReferralCount }} - {{ $torontoReferralPercentage }}% referrals</p>

					<p>Total customers - {{ $combinedTotalCount }}</p>
					<p>Repeat customers - {{ $combinedRepeatCount }} - {{ $combinedPercentage }}% repeats</p>
					<p>Referral customers - {{ $combinedReferralCount }} - {{ $combinedReferralPercentage }}% referrals</p>

				</div>
			</div>
		@endif

		@if(isset($showpicker))
	        <div class='col-sm-4'>
		        <form role="form" action="repeats" id="datepicker-form">
		            <div class="form-group">
						<div class="input-daterange input-group" id="datepicker">
						    <input type="text" class="input-sm form-control" name="start" value='{{ $start }}' />
						    <span class="input-group-addon">to</span>
						    <input type="text" class="input-sm form-control" name="end" value='{{ $end }}'/>
						</div>

<!-- 						<div class='input-group date' id='datepicker'>
		                    <input type='text' name='d' class="form-control" id='date' value='{{ Input::get("m") }} {{ Input::get("y") }}'>
		                    <span class="input-group-addon">
		                    	<span class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div> -->
		            </div>
		            <input type="hidden" name="m" id="date-m">
		            <input type="hidden" name="y" id="date-y">
		            <button type="submit" class="btn btn-default">Submit</button>
		        </form>
	        </div>
	    @endif

	    <div class='col-sm-10'>
	    	<div id='graph' style="width: 100%; height: 600px;">

	    	</div>
	    </div>

	</div>


	<script type="text/javascript">
		var dates = [ {{ $dates }} ];
		var total = [ {{ $total }} ];
		var repeat = [ {{ $repeat }} ];
		var referral = [ {{ $referral }} ];

		var datalabel = {
                enabled: true,
                color: '#FFFFFF',
                style: {
                    // fontSize: '13px',
                    textShadow: '0 0 2px black'
                },
                formatter: function() {
			        if (this.y != 0) {
			          return this.y;
			        } else {
			          return null;
			        }
			    }
            }
	</script>
@stop

@section('scripts')
<script src="http://code.highcharts.com/highcharts.js"></script>

<script type="text/javascript">
    $(function () {

		$('#datepicker input').datepicker({
		    format: 'yyyy-mm-dd',
		    todayHighlight: true
		});

	    $('#graph').highcharts({
	        chart: {
	            type: 'column'
	        },
	        title: {
	            text: 'Repeat Customers'
	        },
   	        credits: {
      			enabled: false
			},
			exporting: {
				enabled: false
			},
	        plotOptions: {
	            line: {
	                dataLabels: {
	                    enabled: true
	                },
	                enableMouseTracking: true
	            },
	            column: {
                    stacking: 'percent',
                    pointPadding: 0,
                    groupPadding: 0,
                },
	        },

	        xAxis: {
	            // type: 'category',
	            labels: {
	                rotation: -45,
	                style: {
	                    fontSize: '13px',
	                }
	            },

   	            categories: dates,
	        },
	        yAxis: {
	            title: {
	                text: 'Customer Percentage'
	            }
	        },

            tooltip: {
                shared: true,
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            },

	        series: [{
	            name: 'Total',
	            data: total,
	            dataLabels: datalabel
	        }, {
	            name: 'Repeat',
	            data: repeat,
	            dataLabels: datalabel
	        }, {
	            name: 'Referral',
	            data: referral,
	            dataLabels: datalabel
	        }]
	    });

    });
</script>


<script type="text/javascript">
$(function () {
        $('#container').highcharts({
            plotOptions: {
                column: {
                    stacking: 'percent',
                    pointPadding: 0,
                    groupPadding: 0,
                },
                line: {
    				lineWidth: 4,
					states: {
                        hover: {
                            lineWidth: 8
                        }
					},
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                }
            },
            colors: ['#202080', '#ff0000', '#90ed7d', '#f7a35c'],
        });

        $('#ratiocontainer').highcharts({
            xAxis: {
                minPadding: 0,
                maxPadding: 0,
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'PERCENT'
                }
            },

        });
    });
</script>
@stop