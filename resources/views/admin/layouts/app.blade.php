<!DOCTYPE html>
<html lang="en">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Wag Enabled">
    <meta name="author" content="Wag Enabled">

    <title>{{ config('app.name', 'Wag Enabled') }} | Admin</title>

    <!-- favicon icon -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{asset('images/favicons/apple-icon-57x57.png')}}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{asset('images/favicons/apple-icon-60x60.png')}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{asset('images/favicons/apple-icon-72x72.png')}}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('images/favicons/apple-icon-76x76.png')}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{asset('images/favicons/apple-icon-114x114.png')}}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{asset('images/favicons/apple-icon-120x120.png')}}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{asset('images/favicons/apple-icon-144x144.png')}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{asset('images/favicons/apple-icon-152x152.png')}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/favicons/apple-icon-180x180.png')}}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{asset('images/favicons/android-icon-192x192.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/favicons/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{asset('images/favicons/favicon-96x96.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicons/favicon-16x16.png')}}">
    <link rel="manifest" href="{{asset('images/favicons/manifest.json')}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{asset('images/favicons/ms-icon-144x144.png')}}">
    <meta name="theme-color" content="#ffffff"> 
    <!-- end favicon icon -->
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Admin theme Css Files -->
    <link href="{{ asset('admin-theme/css/classic.css') }}" rel="stylesheet">
    <link href="{{ asset('admin-theme/css/admin-style.css')."?version=". env("APP_CSS_VERSION", 1) }}" rel="stylesheet">

    <!-- Toastr style -->
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">

    <!-- BEGIN SETTINGS -->
    <!-- You can remove this after picking a style -->
    <style>
        body {
            opacity: 0;
        }
    </style>
    <!-- END SETTINGS -->
</head>

<body>
    <main class="main admin-login-page">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="{{ asset("admin-theme/js/app.js") }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    @stack("scripts")
</body>

</html>