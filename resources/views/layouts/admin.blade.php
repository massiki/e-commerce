<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
  <title>Fikri Amrullah</title>
  <meta charset="utf-8">
  <meta name="author" content="themesflat.com">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/animate.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/animation.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/bootstrap.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/bootstrap-select.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('admin/font/fonts.css') }}">
  <link rel="stylesheet" href="{{ asset('admin/icon/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('logo-fikri.png') }}" type="image/png">
  <link rel="apple-touch-icon" href="{{ asset('logo-fikri.png') }}" type="image/png">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/sweetalert.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/custom.css') }}">
</head>

<body class="body">
  <div id="wrapper">
    <div id="page" class="">
      <div class="layout-wrap">
        @include('components.admin-preload')
        @include('components.admin-sidebar')
        <div class="section-content-right">
          @include('components.admin-navbar')
          <div class="main-content">
            @yield('content')
            @include('components.admin-footer')
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('admin/js/jquery.min.js') }}"></script>
  <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/js/bootstrap-select.min.js') }}"></script>
  <script src="{{ asset('admin/js/sweetalert.min.js') }}"></script>
  <script src="{{ asset('admin/js/apexcharts/apexcharts.js') }}"></script>
  <script src="{{ asset('admin/js/main.js') }}"></script>
  @stack('scripts')

</body>

</html>
