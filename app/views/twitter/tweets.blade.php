<html>
<head>
    <meta charset="utf-8">
    <title>Twitter Search Overview</title>

    <script src="//twemoji.maxcdn.com/twemoji.min.js"></script>
    <script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <style type="text/css">
        img.emoji {
            height: 1em;
            width: 1em;
            margin: 0 .05em 0 .1em;
            vertical-align: -0.1em;
        }
        body {
            font-size: 16px;
        }
        pre.twitterquery h2 {
            margin-top:0;
        }
    </style>
</head>

<body>
<div class='container-fluid'>
    <h1>Twitter Search</h1>
    <div id="addsearch" style="margin-top:10px">
        <form class="form-inline" action="/twitter/add" method="post">
          <div class="form-group">
            <label class="sr-only" for="query">Search</label>
            <input type="text" class="form-control" id="query" name="query" placeholder="Search Query">
          </div>
          <button type="submit" class="btn btn-default">Add</button>
        </form>
    </div>

    <div id="twittersearch">
        {{ $content }}
    </div>
</div>

<script type="text/javascript">
    twemoji.parse(document.body);
</script>

</body>
</html>