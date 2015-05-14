@extends('main')

@section('notifications')
@stop

@section('content')
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-12" style="margin-bottom: 20px">
            <h2>Running Avg Landing Page Metrics</h2>
        </div>
    </div>

    <form role="form" action="aggregate" method="post" id="datepicker-form">
        <div class="row">
            <div class='col-sm-4'>
                <div class="form-group">
                    <div class="input-daterange input-group" id="datepicker">
                        <input type="text" class="input form-control" name="start" value='{{ $start }}'>
                        <span class="input-group-addon">to</span>
                        <input type="text" class="input form-control" name="end" value='{{ date("Y-m-d") }}'>
                    </div>

                </div>
            </div>
            <div class='col-sm-4'>
                <label class="radio-inline"><input type="radio" name="mode" value="avg" checked="checked"> Averages</label>
                <label class="radio-inline"><input type="radio" name="mode" value=""> Totals</label>
                <button type="submit" class="btn btn-primary" style="margin-left: 20px;">Submit</button>
            </div>
            <div class='col-sm-4'>

            </div>
        </div>
    </form>

    <div class="row">
        <div class='col-sm-12'>
            <div id='graph' style="width: 100%; height: 700px;">

            </div>
        </div>
    </div>

    <script type="text/javascript">
        var dates = [ {{ $dates }} ];

@foreach($metrics as $type => $metric)
        var {{ $type.'_seo' }} = [ {{ $metric['seo'] }} ];
        var {{ $type.'_ppc' }} = [ {{ $metric['ppc'] }} ];
        var {{ $type.'_avgseo' }} = [ {{ $metric['avgseo'] }} ];
        var {{ $type.'_avgppc' }} = [ {{ $metric['avgppc'] }} ];
@endforeach


        var datalabel = {
                enabled: true,
                color: 'black',
                style: {
                    fontSize: '13px',                   fontWeight: 'bold',
                    // textShadow: '0 -2px 0px black'
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
            colors: ['#4040a0', '#50ad7d', '#f7a35c', '#000', '#ff0000'],

            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            title: {
                text: 'Landing Page Traffic by Brand'
            },

            plotOptions: {
                spline: {
                    lineWidth: 2,
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                },
                column: {
                    stacking: 'normal',
                    pointPadding: 0,
                    groupPadding: 0,
                },
            },

            xAxis: {
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '11px',
                    }
                },

                categories: dates,
            },

            yAxis: [{
                title: {
                    text: 'Inbound Visits'
                },
            }],

            tooltip: {
                shared: true,
            },

            series: [
            @foreach($metrics as $type => $metric)
                {
                    name: '{{ $type }} SEO {{$mode}}',
                    data: {{ $type."_".$mode."seo" }},
                    type: 'line',
                },
            @endforeach
                // {
                //     name: 'All SEO',
                //     data: all_seo,
                //     type: 'line',
                // },

            ]
        });

    });
</script>


<script type="text/javascript">
    // colors: ['#202080', '#ff0000', '#90ed7d', '#f7a35c'],
</script>
@stop
