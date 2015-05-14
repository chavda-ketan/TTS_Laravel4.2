@extends('main')

@section('notifications')
@stop

@section('content')



    <form role="form" action="repeats" id="datepicker-form">

        <div class="row" style="margin-top:20px;">
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
                <label class="radio-inline"><input type="radio" name="metrics" value="csrh" checked="checked"> CSRH</label>
                <label class="radio-inline"><input type="radio" name="metrics" value="crep"> CREP</label>
                <label class="radio-inline"><input type="radio" name="metrics" value="cref"> CREF</label>
                <label class="radio-inline"><input type="radio" name="metrics" value="all"> All</label>

            </div>
            <div class='col-sm-4'>

                <button type="submit" class="btn btn-primary" style="margin-left: 0px;">Submit</button>
            </div>
        </div>
    </form>

    <div class="row" style="margin-top:20px;">
        <div class='col-sm-12'>
            <div id='graph' style="width: 100%; height: 800px;">

            </div>
        </div>
    </div>

    <script type="text/javascript">
        var dates = [ {{ $dates }} ];
        var total = [ {{ $total }} ];
        var search = [ {{ $search }} ];
        var repeat = [ {{ $repeat }} ];
        var referral = [ {{ $referral }} ];
        var error = [ {{ $error }} ];
        var revenue = [ {{ $revenue }} ];
        var spend = [ {{ $spend }} ];

        var searchtrend = [ {{ $trends['search'] }} ]
        var repeattrend = [ {{ $trends['repeat'] }} ]
        var referraltrend = [ {{ $trends['referral'] }} ]

        var datalabel = {
                enabled: true,
                color: 'white',
                style: {
                    fontSize: '13px',
                    fontWeight: 'bold',
                    textShadow: '0 -2px 0px black'
                },
                // formatter: function() {
                //     if (this.y != 0) {
                //       return Math.round(this.percentage)+'';
                //     } else {
                //       return null;
                //     }
                // }
            }

        var datalabelAvg = {
                enabled: true,
                color: 'black',
                style: {
                    fontSize: '12px',
                },
                formatter: function () {
                    return '$' + this.y;
                }
            }

        var datalabelTrends = {
                enabled: true,
                color: 'black',
                style: {
                    fontSize: '12px',
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
            title: {
                text: 'Repeat Customers & Avg Adwords Spend Per New Customer'
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },

            plotOptions: {
                line: {
                    // lineWidth: 1,
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
                labels: {
                    enabled: false
                },
                title: {
                    text: 'Ratio'
                },
                height: '80%',
                top: '20%'

            },{
                title: {
                    text: 'Avg Spend'
                },
                height: '20%',
                top: '0%',
                // endOnTick: false,
                offset: 0,
                plotLines : [{
                    value : {{ $avg["adwords"] }},
                    color : 'blue',
                    dashStyle : 'dot',
                    width : 2,
                    label : {
                        text : 'Aggregate Average Spend - ${{ $avg["adwords"] }}'
                    }
                }]
            }],
            // },{
            //     title: {
            //         text: 'Trending Avg'
            //     },
            //     endOnTick: false,
            //     height: '18%',
            //     top: '0%',
            //     offset: 0,
            //     min: 0

            // }],

            tooltip: {
                shared: true,
                useHTML: true,
                formatter: function() {
                    var points = '<table class="tip" style="font-size: 16px; "><caption>' + this.x + '</caption><tbody>';

                    points += '<tr><th>Total: </th><td style="text-align:right"><b>'+ this.points[0].total + '</b></td></tr>';

                    $.each(this.points,function(i,point){

                        points += '<tr><th style="color: '+ point.series.color+'">' + point.series.name
                        + ': </th><td style="text-align: right"><b>' + point.y;

                        if (isNaN(Math.round(point.percentage))) {
                            points += '</b></td></tr>';

                        } else {
                            points += '<span style="color: red;"> (' + Math.round(point.percentage) + '%)</span></b></td></tr>';
                        };

                    });


                    // points += '<tr><th>Aggregate Avg Search: </th><td style="text-align:right"><b> {{$avg["search"]}} </b></td></tr>';
                    // points += '<tr><th>Aggregate Avg Repeat: </th><td style="text-align:right"><b> {{$avg["repeat"]}} </b></td></tr>';
                    // points += '<tr><th>Aggregate Avg Referral: </th><td style="text-align:right"><b> {{$avg["referral"]}} </b></td></tr></tbody></table>';

                    return points;
                }
                // pointFormat: '<span style="color:black">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            },

            series: [
            @if($mode === 'csrh' || $mode === 'all')
            {
                name: 'Search',
                data: search,
                dataLabels: datalabel,
                yAxis: 0,
                type: 'column'
            },
            @endif
            @if($mode === 'crep' || $mode === 'all')
             {
                name: 'Repeat',
                data: repeat,
                dataLabels: datalabel,
                yAxis: 0,
                type: 'column'
            },
            @endif
            @if($mode === 'cref' || $mode === 'all')
             {
                name: 'Referral',
                data: referral,
                dataLabels: datalabel,
                color: '#40d040',
                yAxis: 0,
                type: 'column'
            },
            @endif
            // {
            //     name: 'Error',
            //     data: error,
            //     dataLabels: datalabel,
            //     color: 'red',
            //     yAxis: 0,
            //     type: 'column'
            // },
            {
                name: 'Avg Spend',
                data: spend,
                dataLabels: datalabelAvg,
                color: 'red',
                yAxis: 1,
                type: 'line',
            }, {
                name: 'Search Avg',
                data: searchtrend,
                dataLabels: datalabelTrends,
                color: 'red',
                yAxis: 0,
                type: 'line',
            }, {
                name: 'Repeat Avg',
                data: repeattrend,
                dataLabels: datalabelTrends,
                // color: 'red',
                yAxis: 0,
                type: 'line',
            }, {
                name: 'Referral Avg',
                data: referraltrend,
                dataLabels: datalabelTrends,
                // color: 'red',
                yAxis: 0,
                type: 'line',
            },

            ]
        });

    });
</script>


<script type="text/javascript">
    // colors: ['#202080', '#ff0000', '#90ed7d', '#f7a35c'],
</script>
@stop
