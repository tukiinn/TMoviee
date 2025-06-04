<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TMoviee - Xem phim trực tuyến chất lượng cao</title>
    <meta name="description" content="TMoviee - Website xem phim trực tuyến với chất lượng cao, cập nhật nhanh chóng và đa dạng thể loại. Xem phim mới nhất, phim bộ, phim lẻ với phụ đề tiếng Việt.">
    <meta name="keywords" content="xem phim, phim online, phim mới, phim bộ, phim lẻ, phim vietsub, tmoviee">
    <meta property="og:title" content="TMoviee - Xem phim trực tuyến chất lượng cao">
    <meta property="og:description" content="Website xem phim trực tuyến với chất lượng cao, cập nhật nhanh chóng và đa dạng thể loại.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Plyr CDN -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    <style>
        /* Font configuration */
        body {
            font-family: 'Roboto', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        /* Modern transitions */
        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .fade-enter-from,
        .fade-leave-to {
            opacity: 0;
        }

        /* Smooth slide transitions */
        .slide-enter-active,
        .slide-leave-active {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .slide-enter-from {
            transform: translateX(-30px);
        }
        .slide-leave-to {
            transform: translateX(30px);
        }

        /* Modern scale transitions */
        .scale-enter-active,
        .scale-leave-active {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .scale-enter-from,
        .scale-leave-to {
            transform: scale(0.95);
        }

  

        .hover-scale {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-scale:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Modern loading animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Enhanced skeleton loading */
        .skeleton {
            background: linear-gradient(90deg, #2d3748 25%, #4a5568 50%, #2d3748 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1a202c;
        }
        ::-webkit-scrollbar-thumb {
            background: #4a5568;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }

        /* Glass effect */
        .glass {
            background: rgba(26, 32, 44, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(45deg, #f56565, #ed8936);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Modern button styles */
        .btn-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(45deg, #f56565, #ed8936);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-900 text-white">
    <div id="app" class="min-h-screen flex flex-col">
        <main class="flex-grow">
            @yield('content')
        </main>
    </div>
    <!-- Plyr CDN JS -->
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <!-- hls.js CDN for HLS playback -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
</body>
</html> 