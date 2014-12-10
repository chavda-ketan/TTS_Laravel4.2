@extends('main')

@section('notifications')
@stop

@section('content')
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-12" style="margin-bottom: 20px">
            <h2>Landing Page Metrics</h2>
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
                <label class="radio-inline"><input type="radio" name="metrics" value="seo"> Organic</label>
                <label class="radio-inline"><input type="radio" name="metrics" value="ppc"> PPC</label>
                <label class="radio-inline"><input type="radio" name="metrics" value="both" checked='checked'> Both</label>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
                <label class="radio-inline"><input type="radio" name="chartmode" value="column"> Column</label>
                <label class="radio-inline"><input type="radio" name="chartmode" value="line" checked='checked'> Line</label>

            </div>
            <div class='col-sm-4'>

                <button type="submit" class="btn btn-primary" style="margin-left: 0px;">Submit</button>
            </div>
        </div>

        <div class="row">
            <div class='col-md-12'>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <label class="checkbox-inline"><input type="checkbox" name="other"> Other</label>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="playstation"> Playstation
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="xbox"> Xbox
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="promotions"> Promotions
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="locations"> Locations
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="otherbox" name="home"> Home
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
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="iphone-smartphone" name="iphone"> iPhone
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="samsung-smartphone" name="samsung"> Samsung
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="blackberry-smartphone" name="blackberry"> Blackberry
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="htc-smartphone" name="htc"> HTC
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="motorola-smartphone" name="motorola"> Motorola
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="lg-smartphone" name="lg"> LG
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="nokia-smartphone" name="nokia"> Nokia
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="ipod-smartphone" name="ipod"> iPod
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="sony-smartphone" name="sony"> Sony
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
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="macbook-laptop" name="macbook"> Macbook
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="acer-laptop" name="acer"> Acer
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="dell-laptop" name="dell"> Dell
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="gateway-laptop" name="gateway"> Gateway
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="lenovo-laptop" name="lenovo"> Lenovo
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="asus-laptop" name="asus"> Asus
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="hp-compaq-laptop" name="hpcompaq"> HP-Compaq
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="sony-laptop" name="sony"> Sony
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="toshiba-laptop" name="toshiba"> Toshiba
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="lg-laptop" name="lg"> LG
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="msi-laptop" name="msi"> MSI
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="fujitsu-laptop" name="fujitsu"> Fujitsu
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
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="ipad-tablet" name="ipad"> iPad
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="kindle-tablet" name="kindle"> Kindle
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="surface-tablet" name="surface"> Surface
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="asus-tablet" name="asus"> Asus
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="blackberry-tablet" name="blackberry"> Blackberry
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="samsung-tablet" name="samsung"> Samsung
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
@endforeach

        var datalabel = {
                enabled: true,
                color: 'black',
                style: {
                    fontSize: '13px',
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
<script src="http://code.highcharts.com/highcharts.js"></script>

<script type="text/javascript">
    $(function () {

        $('#datepicker input').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true
        });

        $('#smartphones-all').change(function() {
            if($(this).is(":checked")) {
                $('.phonebox').prop('checked', true);
                return;
            }
            $('.phonebox').prop('checked', false);
        });

        $('#laptops-all').change(function() {
            if($(this).is(":checked")) {
                $('.laptopbox').prop('checked', true);
                return;
            }
            $('.laptopbox').prop('checked', false);
        });

        $('#tablets-all').change(function() {
            if($(this).is(":checked")) {
                $('.tabletbox').prop('checked', true);
                return;
            }
            $('.tabletbox').prop('checked', false);
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
                line: {
                    lineWidth: 1,
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
                @elseif($mode == 'seo')
                    {
                        name: '{{ $type }} SEO',
                        data: {{ $type."_seo" }},
                        dataLabels: datalabel,
                        type: '{{ $chartmode }}'
                    },
                @else
                    {
                        name: '{{ $type }} PPC',
                        data: {{ $type."_ppc" }},
                        dataLabels: datalabel,
                        type: '{{ $chartmode }}'
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
