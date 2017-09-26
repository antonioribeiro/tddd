<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Laravel CI-Watcher - Dashboard</title>
		{{--<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />--}}
        {{--<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">--}}
	</head>

    <style>
        {!! file_get_contents(CI_PATH.'/src/public/css/app.css'); !!}
    </style>

	<body>
        <nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse fixed-top">
            <a class="navbar-brand" href="#">Laravel CI-Watcher - Dashboard</a>
        </nav>

        <div id="app">
            <div class="container-fluid">
                <div class="container-fluid">
                    <div class="row row-offcanvas row-offcanvas-right">
                        <div class="col-6 col-md-3 sidebar-offcanvas" id="sidebar">
                            <div class="card bg-inverse card-projects">
                                <div class="card-block text-center">
                                    <span class="projects-title">
                                        Projects
                                    </span>
                                </div>
                            </div>

                            <projects class="list-group">
                                <span class="card-projects-items" id="projects">
                                </span>
                            </projects>
                        </div><!--/span-->

                        <div class="col-12 col-sm-9">
                            <div class="table-container">
                                <div class="table-responsive" id="table-container">
                                    <tests>
                                    </tests>
                                </div>
                            </div>
                        </div><!--/span-->
                    </div><!--/row-->
                </div><!--/.container-->
            </div>
        </div>

        {{--<div class="container-fluid">--}}
            {{--<div class="row row-offcanvas row-offcanvas-right">--}}
                {{--<div class="col-6 col-md-3 sidebar-offcanvas" id="sidebar">--}}
                    {{--<div class="card bg-inverse card-projects">--}}
                        {{--<div class="card-block text-center">--}}
                            {{--<span class="projects-title">--}}
                                {{--Projects--}}
                            {{--</span>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<ul class="list-group">--}}
                        {{--<span class="card-projects-items" id="projects">--}}
                        {{--</span>--}}
                    {{--</ul>--}}
                {{--</div><!--/span-->--}}

                {{--<div class="col-12 col-sm-9">--}}
                    {{--<div class="table-container">--}}
                        {{--<div class="table-responsive" id="table-container">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div><!--/span-->--}}
            {{--</div><!--/row-->--}}
        {{--</div><!--/.container-->--}}

        <script src="//cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
        {{--<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>--}}

        <script>
            {!! file_get_contents(CI_PATH.'/src/public/js/app.js'); !!}
        </script>

        <script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.0/react-with-addons.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/react/0.12.0/JSXTransformer.js" type="text/javascript"></script>

        @include('pragmarx/ci::react-app')

        @if(config('app.env') == 'local')
            <script src="//localhost:35729/livereload.js"></script>
        @endif
    </body>
</html>
