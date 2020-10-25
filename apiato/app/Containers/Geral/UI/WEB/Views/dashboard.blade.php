<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name') }}</title>

	<link rel="stylesheet" href="{{ asset(mix('container/admin/css/main.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('css/iconfont.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('css/material-icons/material-icons.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('css/vuesax.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('css/prism-tomorrow.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('container/admin/css/app.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('css/loader.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('container/admin/css/dashboard.css')) }}">

	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ asset('images/logo/favicon.png') }}">
</head>

<body>
	<noscript>
		<strong>{{ config('app.name') }} necessita do JavaScript ativado.</strong>
	</noscript>
	<div id="app">
	</div>
	<script src="{{ asset(mix('container/admin/js/dashboard/manifest.js')) }}"></script>
	<script src="{{ asset(mix('container/admin/js/dashboard/vendor.js')) }}"></script>
	<script src="{{ asset(mix('container/admin/js/dashboard.js')) }}"></script>
</body>

</html>
