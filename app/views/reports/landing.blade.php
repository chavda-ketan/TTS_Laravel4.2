@extends('main')

@section('notifications')
@stop

@section('content')
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-12" style="margin-bottom: 20px">
            <h2>Landing Page Metrics</h2>
        </div>
        @if(isset($showpicker))
            <div class='col-sm-4'>
                <form role="form" action="landing" id="datepicker-form">
                    <div class="form-group">
                        <div class="input-daterange input-group" id="datepicker">
                            <input type="text" class="input-sm form-control" name="start" value='{{ $start }}'>
                            <span class="input-group-addon">to</span>
                            <input type="text" class="input-sm form-control" name="end" value='{{ date("Y-m-d") }}'>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        @endif

        <div class='col-sm-12'>
            <div id='graph' style="width: 100%; height: 800px;">

            </div>
        </div>

    </div>

    <script type="text/javascript">
        var dates = [ {{ $dates }} ];
        var general = [ {{ $dates }} ];
        var repair = [ {{ $dates }} ];


        var datalabel = {
                enabled: true,
                color: 'white',
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    textShadow: '0 -2px 0px black'
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
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },

            plotOptions: {
                line: {
                    lineWidth: 1,
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
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '11px',
                    }
                },

                categories: dates,
            },

            yAxis: [{
                labels: {
                    enabled: false
                },
                title: {
                    text: 'Ratio'
                },
                height: '60%',
                top: '40%'

            },{
                title: {
                    text: 'Avg Spend'
                },
                height: '20%',
                top: '20%',
                // endOnTick: false,
                offset: 0,
                plotLines : [{
                    color : 'blue',
                    dashStyle : 'dot',
                    width : 2,
                    label : {
                        text : 'Aggregate Average Spend - $'
                    }
                }]
            },{
                title: {
                    text: 'Trending Avg'
                },
                endOnTick: false,
                height: '18%',
                top: '0%',
                offset: 0,
                min: 0

            }],

            tooltip: {
                shared: true,
            },

            series: [{
                name: 'General',
                data: general,
                dataLabels: datalabel,
                yAxis: 0,
                type: 'column'
            }, {
                name: 'Repair',
                data: repair,
                dataLabels: datalabel,
                yAxis: 0,
                type: 'column'
            }
            ]
        });

    });
</script>


<script type="text/javascript">
    // colors: ['#202080', '#ff0000', '#90ed7d', '#f7a35c'],
</script>
@stop
