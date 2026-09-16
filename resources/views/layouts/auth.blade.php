<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Masuk' }} · SMK BINA UTAMA KENDAL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#eef2f7] relative" x-data="pullToRefresh()">
    {{-- Pull to Refresh Loading Indicator --}}
    <div
        x-show="pullDistance > 0"
        class="fixed top-0 left-0 w-full z-[100] flex justify-center pointer-events-none transition-all duration-150"
        :style="`transform: translateY(${Math.min(pullDistance, 80)}px)`"
        style="display: none;"
    >
        <div class="bg-white rounded-full p-3 shadow-xl border border-slate-100 flex items-center justify-center">
            <i class="ti ti-refresh text-[#2c68f5] text-xl" :class="pullDistance > 60 ? 'animate-spin' : ''" :style="`transform: rotate(${pullDistance * 2}deg)`"></i>
        </div>
    </div>

    <div
        class="pointer-events-none fixed inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(44,104,245,.12),transparent_50%),radial-gradient(ellipse_at_bottom_right,_rgba(255,213,0,.14),transparent_50%)]"
    ></div>

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-school-control focus:bg-school-navy focus:px-4 focus:py-3 focus:text-sm focus:font-semibold focus:text-white">
        Lewati ke formulir masuk
    </a>

    <main
        id="main-content"
        class="relative grid min-h-screen place-items-center px-4 py-12"
        @touchstart="touchStart($event)"
        @touchmove="touchMove($event)"
        @touchend="touchEnd($event)"
    >
        @yield('content')
    </main>

    <script>
        function pullToRefresh() {
            return {
                startY: 0,
                pullDistance: 0,
                isRefreshing: false,

                touchStart(e) {
                    if (window.scrollY === 0) {
                        this.startY = e.touches[0].pageY;
                    }
                },

                touchMove(e) {
                    if (window.scrollY === 0 && !this.isRefreshing) {
                        const currentY = e.touches[0].pageY;
                        const distance = currentY - this.startY;
                        if (distance > 0) {
                            this.pullDistance = distance * 0.4; // Friction
                            if (this.pullDistance > 10) {
                                // Prevent scrolling while pulling
                                if (e.cancelable) e.preventDefault();
                            }
                        }
                    }
                },

                touchEnd() {
                    if (this.pullDistance > 60) {
                        this.isRefreshing = true;
                        this.pullDistance = 70;
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        this.pullDistance = 0;
                    }
                }
            }
        }
    </script>
</body>
</html>
