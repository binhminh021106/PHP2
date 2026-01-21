<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Shop Home')</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  {{-- Nơi để các trang con chèn thêm CSS riêng nếu cần --}}
  @yield('styles')
</head>

<body class="bg-light">

  <!-- Header / Navbar -->
  @include('partials.client.header')

  <!-- Main Content -->
  <main class="container py-4">
    @yield('content')
  </main>

  <!-- Footer -->
  @include('partials.client.footer')

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  {{-- Nơi để các trang con chèn thêm JS riêng nếu cần --}}
  @yield('scripts')
</body>
</html>