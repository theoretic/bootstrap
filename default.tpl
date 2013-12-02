<!doctype html>
<html>
    <head>
	<title>Travelhead ~ [*longtitle*]</title>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->

		<link rel=stylesheet href=/assets/templates/bootstrap/css/core/>
		<script src=/assets/templates/bootstrap/js/core/></script>
	<body>

	<div class=container>

		<div class="navbar">
			<div class=container>
				<div class=logo>
					<a href=/>
						<img src=/assets/templates/bootstrap/img/travelhead.png alt="Travelhead" title="Travelhead">
					</a>
				</div>
				<div class=menu id=menu>
					{{menu}}
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-9">
				<h1 class=pagetitle>[*longtitle*]</h1>
			[*content*]
		</div>

		<div class="col-sm-3">
			{{right}}
		</div>


	</div>

	{{footer}}

	</body>
</html>