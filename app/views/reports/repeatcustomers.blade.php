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

<!--                        <div class='input-group date' id='datepicker'>
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

        <div class='col-sm-12'>
            <div id='graph' style="width: 100%; height: 700px;">

            </div>
        </div>

    </div>

    <script type="text/javascript">
        var dates = [ {{ $dates }} ];
        var total = [ {{ $total }} ];
        var repeat = [ {{ $repeat }} ];
        var referral = [ {{ $referral }} ];
        var revenue = [ {{ $revenue }} ];
        var spend = [ {{ $spend }} ];

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

        var datalabelAvg = {
                enabled: true,
                color: 'black',
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold',
                    // textShadow: '0 1px 1px black'
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
            title: {
                text: 'Repeat Customers & Average Revenue Per Customer'
            },
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
                // type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '12px',
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
                height: '70%',
                top: '30%'

            },{
                title: {
                    text: 'Avg Spend'
                },
                height: '30%',
                bottom: '70%',
                offset: 0,
                plotLines : [{
                    value : {{ $avg }},
                    color : 'blue',
                    dashStyle : 'dot',
                    width : 2,
                    label : {
                        text : 'Aggregate Average Spend - ${{$avg}}'
                    }
                }]
            }],

            tooltip: {
                shared: true,
                useHTML: true,
                formatter: function() {
                    var points = '<table class="tip"><caption>' + this.x + '</caption><tbody>';

                    $.each(this.points,function(i,point){
                        points += '<tr><th style="color: black">' + point.series.name + ': </th><td style="text-align: right"><b>' + point.y + '</b> (' + Math.round(point.percentage) + '%)</td></tr>';
                    });

                    points += '<tr><th>Total: </th><td style="text-align:right"><b>' + this.points[0].total + '</b></td></tr></tbody></table>';
                    return points;
                }
                // pointFormat: '<span style="color:black">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            },

            series: [{
                name: 'Search',
                data: total,
                dataLabels: datalabel,
                yAxis: 0,
                type: 'column'
            }, {
                name: 'Repeat',
                data: repeat,
                dataLabels: datalabel,
                yAxis: 0,
                type: 'column'
            }, {
                name: 'Referral',
                data: referral,
                dataLabels: datalabel,
                color: '#40d040',
                yAxis: 0,
                type: 'column'
            },{
                name: 'Avg Spend',
                data: spend,
                dataLabels: datalabelAvg,
                color: 'red',
                yAxis: 1,
                type: 'line',

            }]
        });

    });
</script>


<script type="text/javascript">
    // colors: ['#202080', '#ff0000', '#90ed7d', '#f7a35c'],
</script>
@stop
