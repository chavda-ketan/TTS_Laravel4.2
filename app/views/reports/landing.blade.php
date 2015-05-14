@extends('main')

@section('notifications')
@stop

@section('content')
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-12" style="margin-bottom: 20px">
            <h2>Landing Page Metrics - <a href="aggregate" target="_blank">Trends</a></h2>
        </div>
    </div>

    <form role="form" action="landing" method="post" id="datepicker-form">
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
                <label class="radio-inline"><input type="radio" name="metrics" value="seo" checked="checked"> Organic</label>
                <label class="radio-inline"><input type="radio" name="metrics" value="ppc"> PPC</label>
                <label class="radio-inline"><input type="radio" name="metrics" value="both"> Both</label>
                <label class="radio-inline"><input type="radio" name="chartmode" value="column" checked="checked"> Column</label>
                <label class="radio-inline"><input type="radio" name="chartmode" value="spline"> Line</label>

            </div>
            <div class='col-sm-4'>

                <button type="submit" class="btn btn-primary" style="margin-left: 0px;">Submit</button>
            </div>
        </div>

        <div class="row">
            <div class='col-md-12'>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <label class="checkbox-inline"><input type="checkbox" id="other" name="other"> Other</label>
                    </div>
                    <div id="otherbox" class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="playstation" disabled> Playstation
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="xbox" disabled> Xbox
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="promotions" disabled> Promotions
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="locations" disabled> Locations
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="home" disabled> Home
                            </label>
                        </div>
                    </div>
                </div>
            </div>



            <div class='col-md-4'>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <label class="checkbox-inline"><input type="checkbox" id="smartphones" name="smartphones"> Smartphones</label>
                        <label class="checkbox-inline"><input type="checkbox" id="smartphones-all" name="smartphones-all"> All Smartphones</label>
                    </div>
                    <div id="phonebox" class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="iphone-smartphone" name="iphone" disabled> iPhone
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="samsung-smartphone" name="samsung" disabled> Samsung
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="blackberry-smartphone" name="blackberry" disabled> Blackberry
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="htc-smartphone" name="htc" disabled> HTC
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="motorola-smartphone" name="motorola" disabled> Motorola
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="lg-smartphone" name="lg" disabled> LG
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="nokia-smartphone" name="nokia" disabled> Nokia
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="ipod-smartphone" name="ipod" disabled> iPod
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="sony-smartphone" name="sony" disabled> Sony
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class='col-md-4'>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <label class="checkbox-inline"><input type="checkbox" id="laptops" name="laptops"> Laptops</label>
                        <label class="checkbox-inline"><input type="checkbox" id="laptops-all" name="laptops-all"> All Laptops</label>
                    </div>
                    <div id="laptopbox" class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="macbook-laptop" name="macbook" disabled> Macbook
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="acer-laptop" name="acer" disabled> Acer
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="dell-laptop" name="dell" disabled> Dell
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="gateway-laptop" name="gateway" disabled> Gateway
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="lenovo-laptop" name="lenovo" disabled> Lenovo
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="asus-laptop" name="asus" disabled> Asus
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="hp-compaq-laptop" name="hpcompaq" disabled> HP-Compaq
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="sony-laptop" name="sony" disabled> Sony
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="toshiba-laptop" name="toshiba" disabled> Toshiba
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="lg-laptop" name="lg" disabled> LG
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="msi-laptop" name="msi" disabled> MSI
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="fujitsu-laptop" name="fujitsu" disabled> Fujitsu
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class='col-md-4'>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <label class="checkbox-inline"><input type="checkbox" id="tablets" name="tablets"> Tablets</label>
                        <label class="checkbox-inline"><input type="checkbox" id="tablets-all" name="tablets-all"> All Tablets</label>
                    </div>
                    <div id="tabletbox" class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="ipad-tablet" name="ipad" disabled> iPad
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="kindle-tablet" name="kindle" disabled> Kindle
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="surface-tablet" name="surface" disabled> Surface
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="asus-tablet" name="asus" disabled> Asus
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="blackberry-tablet" name="blackberry" disabled> Blackberry
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="samsung-tablet" name="samsung" disabled> Samsung
                            </label>
                        </div>
                    </div>
                </div>
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

        $('#other').change(function() {
            if($(this).is(":checked")) {
                $("#otherbox :input").attr("disabled", false);
                return;
            }
            $('#otherbox :input').prop('checked', false);
            $("#otherbox :input").attr("disabled", true);
        });

        $('#smartphones').change(function() {
            if($(this).is(":checked")) {
                $("#phonebox :input").attr("disabled", false);
                return;
            }
            $('#phonebox :input').prop('checked', false);
            $("#phonebox :input").attr("disabled", true);
        });

        $('#laptops').change(function() {
            if($(this).is(":checked")) {
                $("#laptopbox :input").attr("disabled", false);
                return;
            }
            $('#laptopbox :input').prop('checked', false);
            $("#laptopbox :input").attr("disabled", true);
        });

        $('#tablets').change(function() {
            if($(this).is(":checked")) {
                $("#tabletbox :input").attr("disabled", false);
                return;
            }
            $('#tabletbox :input').prop('checked', false);
            $("#tabletbox :input").attr("disabled", true);
        });


        $('#smartphones-all').change(function() {
            if($(this).is(":checked")) {
                $('#phonebox :input').prop('checked', true);
                return;
            }
            $('#phonebox :input').prop('checked', false);
        });

        $('#laptops-all').change(function() {
            if($(this).is(":checked")) {
                $('#laptopbox :input').prop('checked', true);
                return;
            }
            $('#laptopbox :input').prop('checked', false);
        });

        $('#tablets-all').change(function() {
            if($(this).is(":checked")) {
                $('#tabletbox :input').prop('checked', true);
                return;
            }
            $('#tabletbox :input').prop('checked', false);
        });

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
                @if($mode == 'both')
                    {
                        name: '{{ $type }} SEO',
                        data: {{ $type."_seo" }},
                        dataLabels: datalabel,
                        type: '{{ $chartmode }}'
                    },
                    {
                        name: '{{ $type }} PPC',
                        data: {{ $type."_ppc" }},
                        dataLabels: datalabel,
                        type: '{{ $chartmode }}'
                    },
                    {
                        name: '{{ $type }} SEO Avg',
                        data: {{ $type."_avgseo" }},
                        // dataLabels: datalabel,
                        type: 'line',
                        color: 'red'
                    },
                    {
                        name: '{{ $type }} PPC Avg',
                        data: {{ $type."_avgppc" }},
                        // dataLabels: datalabel,
                        type: 'line',
                        color: 'orange'
                    },
                @elseif($mode == 'seo')
                    {
                        name: '{{ $type }} SEO',
                        data: {{ $type."_seo" }},
                        dataLabels: datalabel,
                        type: '{{ $chartmode }}'
                    },
                    {
                        name: '{{ $type }} SEO Avg',
                        data: {{ $type."_avgseo" }},
                        // dataLabels: datalabel,
                        type: 'line',
                        color: 'red'
                    },

                @else
                    {
                        name: '{{ $type }} PPC',
                        data: {{ $type."_ppc" }},
                        dataLabels: datalabel,
                        type: '{{ $chartmode }}'
                    },
                    {
                        name: '{{ $type }} PPC Avg',
                        data: {{ $type."_avgppc" }},
                        // dataLabels: datalabel,
                        type: 'line',
                        color: 'orange'
                    },
                @endif
            @endforeach
            ]
        });

    });
</script>


<script type="text/javascript">
    // colors: ['#202080', '#ff0000', '#90ed7d', '#f7a35c'],
</script>
@stop
