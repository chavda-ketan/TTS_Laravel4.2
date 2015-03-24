@extends('main')

@section('notifications')
@stop

@section('content')
</div>
<div class="container-fluid">
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-12" style="margin-bottom: 20px">
            <h2>SEO vs PPC Delta</h2>
        </div>
    </div>
    <div class="row">
        <div class='col-sm-12'>
            <div id='graph' style="width: 100%; height: 700px;">

            </div>
        </div>
    </div>

    <script type="text/javascript">
        var datalabel = {
                enabled: true,
                color: 'black',
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold',
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
<script src="http://code.highcharts.com/stock/highstock.js"></script>
<script src="http://code.highcharts.com/stock/modules/exporting.js"></script>

<script type="text/javascript">
    $(function () {

        $('#graph').highcharts('StockChart',{
            rangeSelector : {
                selected : 1
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            chart: {
                type: 'column'
            },
            title: {
                enabled: false,
                // text: 'Google Analytics Data History'
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
                    // pointPadding: 0,
                    // groupPadding: 0,
                },
            },

            // xAxis: {
            //     labels: {
            //         style: {
            //             fontSize: '11px',
            //         }
            //     },

            // },

            // yAxis: [{
            //     title: {
            //         text: 'Inbound Visits'
            //     },
            // }],

            tooltip: {
                shared: true,
            },

            colors: ['#30aadd','#dd7070'],

            series: [
                {
                    name: 'SEO Delta',
                    data: [ {{ $seo }} ],
                    dataLabels: datalabel,
                },
                {
                    name: 'PPC Delta',
                    data: [ {{ $ppc }} ],
                    dataLabels: datalabel,
                }
            ]
        });

    });
</script>


<script type="text/javascript">
    // colors: ['#202080', '#ff0000', '#90ed7d', '#f7a35c'],
</script>
@stop
