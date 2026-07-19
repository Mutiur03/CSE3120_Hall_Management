<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#eceae7">
    <title>@yield('title', 'Dashboard') | Hall Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div class="wrapper">
        @include('partials.sidebar')
        <div class="main-content">
            @include('partials.topbar')
            <div class="content-wrapper" id="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert" aria-live="polite">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss"></button>
                    </div>
                @endif

                @yield('content')
            </div>
            @include('partials.footer')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
    <script>
        const toastBase = {
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true,
            background: '#f7f6f4',
            color: '#1e2228',
        };
        @if(session('success'))
            Swal.fire({
                ...toastBase,
                icon: 'success',
                title: @json(session('success')),
            });
        @endif
        @if(session('error'))
            Swal.fire({
                ...toastBase,
                icon: 'error',
                title: @json(session('error')),
            });
        @endif
    </script>
</body>
</html>
