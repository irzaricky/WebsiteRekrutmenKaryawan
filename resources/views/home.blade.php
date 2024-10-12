<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hello</title>
    @vite('resources/css/app.css')
</head>

<body>
    <div class="container">
        <h1>Selamat Datang di Aplikasi Rekrutmen</h1>

        @if (Auth::guest())
            <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
            <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
        @elseif(Auth::user()->role === 'admin')
            <a href="{{ route('dashboard') }}" class="btn btn-success">Masuk ke Dashboard</a>
        @elseif(Auth::user()->role === 'calon_karyawan')
            <a href="{{ route('profile') }}" class="btn btn-info">Masuk ke Profil Calon Karyawan</a>
        @endif
    </div>
</body>

</html>
