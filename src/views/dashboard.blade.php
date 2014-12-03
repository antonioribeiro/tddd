<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Continuous Integration Package - Dashboard</title>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
		<link href="assets/css/app.css" media="all" rel="stylesheet" type="text/css" />
	</head>

    <style>
        @include('pragmarx/ci::css.app')
    </style>

	<body>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Continuous Integration Package - Dashboard</a>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar">
                    <div class="well well-sm text-center bg-primary" style="background-color: black;">
                        <h4>Projects</h4>
                    </div>

                    <div id="projects">

                    </div>
                </div>

                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <div class="table-responsive" id="table-container">
                    </div>
                </div>
            </div>
        </div>

		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.js" type="text/javascript"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.12.0/react-with-addons.js" type="text/javascript"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.12.0/JSXTransformer.js" type="text/javascript"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js" type="text/javascript"></script>

		<script type="text/jsx">
			@include('pragmarx/ci::javascript.reactjs-app')
		</script>
	</body>
</html>
