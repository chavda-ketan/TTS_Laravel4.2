<!DOCTYPE html>
<html lang="en" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @section('title')
            TechKnow Space Internal
        @show
    </title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/datepicker3.css" rel="stylesheet">
    <link href="/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="/css/plugins/timeline.css" rel="stylesheet">
    <link href="/css/sb-admin-2.css" rel="stylesheet">
    <link href="/fonts/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <!-- Notifications -->
    <!-- include('notifications') -->
    <!-- ./ notifications -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">TechKnow Space</a>
            </div>
        </nav>

        <div class="container">

            <!-- Content -->
            @yield('content')
            <!-- ./ content -->

        </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/bootstrap-datepicker.js"></script>
    <script src="/js/jquery.tablesorter.js"></script>
    <script src="/js/jquery.tablesorter.widgets.js"></script>

    <script src="/js/plugins/metisMenu/metisMenu.min.js"></script>
    <script src="/js/plugins/morris/raphael.min.js"></script>

    <script src="/js/sb-admin-2.js"></script>

    <script src="/js/print.js"></script>
    <script src="/js/jquery.tabletojson.js"></script>

    @yield('scripts')
</body>
</html>
