<html lang="{{Config::get("app.locale")}}">
    <head>
         <meta data-n-head="true" charset="utf-8"/>
        <title>{{config("app.name")}} - @yield('title')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" type="text/css" href="/admin-vi/css/app.css">
    </head>
    <body>
        <div id="app">
            <master-top></master-top>
            <div class="page">
                <master-menu></master-menu>
                <div class="container-fluid clearfix">
                    <div>
                        <router-view class="view"></router-view>
                    </div>
                </div>
            </div>
        </div>
         <script type="text/javascript" src="/admin-vi/js/manifest.js"></script>
         <script type="text/javascript" src="/admin-vi/js/vendor.js"></script>
         <script type="text/javascript" src="/admin-vi/js/app.js"></script>
    </body>
</html>