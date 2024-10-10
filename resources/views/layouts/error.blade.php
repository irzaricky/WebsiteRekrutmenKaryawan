<!DOCTYPE html>
<html lang="en" class="light scroll-smooth group" data-layout="vertical" data-sidebar="light" data-sidebar-size="lg" data-mode="light" data-topbar="light" data-skin="default" data-navbar="sticky" data-content="fluid" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>404 | StarCode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="Minimal Admin & Dashboard Template" name="description">
    <meta content="StarCode Kh" name="author">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::to('assets/images/favicon.ico') }}">
    <!-- Layout config Js -->
    <script src="{{ URL::to('assets/js/layout.js') }}"></script>
    <!-- StarCode CSS -->
    <link rel="stylesheet" href="{{ URL::to('assets/css/starcode2.css') }}">
</head>

<body class="flex items-center justify-center min-h-screen py-16 bg-cover bg-auth-pattern dark:bg-auth-pattern-dark font-public bg-white/70 dark:bg-zink-500/70">

    @yield('content')

    <script src='{{ URL::to('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}'></script>
    <script src="{{ URL::to('assets/libs/%40popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ URL::to('assets/libs/tippy.js/tippy-bundle.umd.min.js') }}"></script>
    <script src="{{ URL::to('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ URL::to('assets/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::to('assets/libs/lucide/umd/lucide.js') }}"></script>
    <script src="{{ URL::to('assets/js/starcode.bundle.js') }}"></script>
</body>

</html>