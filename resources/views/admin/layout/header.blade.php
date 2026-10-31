<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    {{-- Hide the loader even if a third-party script fails to load. --}}
    <div id="app-preloader" class="preloader flex-column justify-content-center align-items-center" role="status" aria-label="جارٍ تحميل الصفحة">
        <i class="fas fa-cut fa-3x text-secondary" aria-hidden="true"></i>
        <span class="mt-3 text-sm text-muted">جارٍ تجهيز الصفحة...</span>
    </div>
    <script>
        (() => {
            let hidden = false;
            const hidePreloader = () => {
                if (hidden) return;
                hidden = true;
                const preloader = document.getElementById('app-preloader');
                if (preloader) {
                    preloader.classList.add('preloader-hidden');
                    window.setTimeout(() => preloader.remove(), 300);
                }
                document.body?.classList.remove('hold-transition');
            };

            window.addEventListener('load', hidePreloader, { once: true });
            // Do not let a missing/blocked plugin keep the whole admin area covered.
            window.setTimeout(hidePreloader, 1600);
        })();
    </script>
