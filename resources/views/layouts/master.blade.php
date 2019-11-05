<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Document</title>

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
  @include('layouts.navbar')

  <main role="main" class="container">
    @yield('css')
    <div class="starter-template">
      @yield('content')
    </div>
  </main>

  <script src="{{ asset('js/app.js') }}"></script>
  @stack('js')
</body>
</html>