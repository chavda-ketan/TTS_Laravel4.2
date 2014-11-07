@extends('main')

@section('notifications')
@stop

@section('content')
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-12" style="margin-bottom: 20px">
            <h2>Landing Page Metrics</h2>
        </div>
    </div>

    <form role="form" action="landing" id="datepicker-form">
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
                <button type="submit" class="btn btn-default btn-primary">Submit</button>
            </div>
        </div>

        <div class="row">
            <div class='col-md-4'>
                <div class="panel panel-primary">
                    <div class="panel-heading"><label><input type="checkbox" id="smartphone" value="option1"> Smartphones</label></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox1" value="option1"> iPhone
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox2" value="option2"> Samsung
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox3" value="option3"> Blackberry
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox3" value="option3"> HTC
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox3" value="option3"> Motorola
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox3" value="option3"> LG
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox3" value="option3"> Nokia
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox3" value="option3"> iPod
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="phonebox" id="inlineCheckbox3" value="option3"> Sony
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class='col-md-4'>
                <div class="panel panel-success">
                    <div class="panel-heading"><label><input type="checkbox" id="laptop" value="option1"> Laptops</label></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox1" value="option1"> Macbook
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox2" value="option2"> Acer
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox3" value="option3"> Dell
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox3" value="option3"> Gateway
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox1" value="option1"> Lenovo
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox2" value="option2"> Asus
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox3" value="option3"> HP-Compaq
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox3" value="option3"> Sony
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox1" value="option1"> Toshiba
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox2" value="option2"> LG
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox3" value="option3"> MSI
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="laptopbox" id="inlineCheckbox3" value="option3"> Fujitsu
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class='col-md-4'>
                <div class="panel panel-info ">
                    <div class="panel-heading"><label><input type="checkbox" id="tablet" value="option1"> Tablets</label></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="inlineCheckbox1" value="option1"> iPad
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="inlineCheckbox2" value="option2"> Kindle
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="inlineCheckbox3" value="option3"> Surface
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="inlineCheckbox3" value="option3"> Asus
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="inlineCheckbox1" value="option1"> Blackberry
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="tabletbox" id="inlineCheckbox2" value="option2"> Samsung
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

        <div class='col-sm-12'>
            <div id='graph' style="width: 100%; height: 800px;">

            </div>
        </div>

    </div>

    <script type="text/javascript">
        var dates = [ {{ $dates }} ];

        @if(isset($metrics['iphone']))
            var iphone = [ {{ $metrics['iphone'] }} ];
        @endif

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

        $('#smartphone').change(function() {
            if($(this).is(":checked")) {
                $('.phonebox').prop('checked', true);
                return;
            }
            $('.phonebox').prop('checked', false);
        });

        $('#laptop').change(function() {
            if($(this).is(":checked")) {
                $('.laptopbox').prop('checked', true);
                return;
            }
            $('.laptopbox').prop('checked', false);
        });

        $('#tablet').change(function() {
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
                {
                    name: '{{ $type }}',
                    data: {{ $type }},
                    dataLabels: datalabel,
                    type: 'column'
                },
            @endforeach
            ]
        });

    });
</script>


<script type="text/javascript">
    // colors: ['#202080', '#ff0000', '#90ed7d', '#f7a35c'],
</script>
@stop
