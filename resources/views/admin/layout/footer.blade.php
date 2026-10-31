
<footer class="main-footer">
    <strong>{{ config('app.name', 'باربر هاوس') }}</strong>
    <span class="d-none d-sm-inline-block text-muted mr-2">
        إدارة الصالون والحجوزات
    </span>
</footer>
</div>

{{-- Core Assets --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@livewireScripts
@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('admin-sidebar');
    const toggle = document.getElementById('adminSidebarToggle');
    const backdrop = document.getElementById('adminSidebarBackdrop');

    const desktopMedia = window.matchMedia('(min-width: 992px)');

    /*
    |--------------------------------------------------------------------------
    | Sidebar Helpers
    |--------------------------------------------------------------------------
    */

    const closeSidebar = ({ restoreFocus = false } = {}) => {
        document.body.classList.remove('admin-sidebar-open');

        if (sidebar) {
            sidebar.setAttribute('aria-hidden', 'true');
            sidebar.setAttribute('inert', '');
        }

        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'فتح القائمة الجانبية');
            toggle.setAttribute('title', 'فتح القائمة الجانبية');
        }

        if (backdrop) {
            backdrop.hidden = true;
        }

        if (restoreFocus) {
            toggle?.focus();
        }
    };

    const openSidebar = () => {
        document.body.classList.add('admin-sidebar-open');

        if (sidebar) {
            sidebar.setAttribute('aria-hidden', 'false');
            sidebar.removeAttribute('inert');
        }

        if (toggle) {
            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'إغلاق القائمة الجانبية');
            toggle.setAttribute('title', 'إغلاق القائمة الجانبية');
        }

        if (backdrop) {
            backdrop.hidden = false;
        }

        sidebar
            ?.querySelector('.nav-sidebar a[href], .nav-sidebar button')
            ?.focus({ preventScroll: true });
    };

    const updateDesktopToggle = () => {
        if (!toggle) return;

        const isExpanded = !document.body.classList.contains('sidebar-collapse');

        toggle.setAttribute('aria-expanded', String(isExpanded));

        toggle.setAttribute(
            'aria-label',
            isExpanded ? 'تصغير القائمة الجانبية' : 'توسيع القائمة الجانبية'
        );

        toggle.setAttribute(
            'title',
            isExpanded ? 'تصغير القائمة الجانبية' : 'توسيع القائمة الجانبية'
        );
    };

    /*
    |--------------------------------------------------------------------------
    | Responsive Sidebar
    |--------------------------------------------------------------------------
    */

    const syncSidebarWithViewport = () => {
        if (desktopMedia.matches) {
            document.body.classList.remove('admin-sidebar-open');

            // Desktop starts with the normal sidebar.
            document.body.classList.remove('sidebar-collapse');

            sidebar?.setAttribute('aria-hidden', 'false');
            sidebar?.removeAttribute('inert');

            if (backdrop) {
                backdrop.hidden = true;
            }

            updateDesktopToggle();

            return;
        }

        // Mobile and tablet start with a closed sidebar.
        closeSidebar();
    };

    syncSidebarWithViewport();

    if (desktopMedia.addEventListener) {
        desktopMedia.addEventListener('change', syncSidebarWithViewport);
    } else {
        desktopMedia.addListener(syncSidebarWithViewport);
    }

    /*
    |--------------------------------------------------------------------------
    | Sidebar Toggle Button
    |--------------------------------------------------------------------------
    */

    toggle?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (desktopMedia.matches) {
            // Desktop: Normal sidebar <-> Icon-only sidebar.
            document.body.classList.toggle('sidebar-collapse');

            document.body.classList.remove('admin-sidebar-open');

            sidebar?.setAttribute('aria-hidden', 'false');
            sidebar?.removeAttribute('inert');

            if (backdrop) {
                backdrop.hidden = true;
            }

            updateDesktopToggle();

            return;
        }

        // Mobile / Tablet: Open or close drawer.
        const isOpen = document.body.classList.contains('admin-sidebar-open');

        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Backdrop Click
    |--------------------------------------------------------------------------
    */

    backdrop?.addEventListener('click', () => {
        closeSidebar({ restoreFocus: true });
    });

    /*
    |--------------------------------------------------------------------------
    | Sidebar Navigation
    |--------------------------------------------------------------------------
    */

    sidebar?.addEventListener('click', (event) => {
        const target = event.target instanceof Element
            ? event.target
            : null;

        if (!target) return;

        // Submenu Toggle
        const menuToggle = target.closest('[data-sidebar-toggle]');

        if (menuToggle) {
            event.preventDefault();
            event.stopPropagation();

            const menuItem = menuToggle.closest('.nav-item');

            if (!menuItem) return;

            const isOpen = menuItem.classList.toggle('menu-open');

            menuToggle.setAttribute('aria-expanded', String(isOpen));

            return;
        }

        // Normal Navigation Links
        const link = target.closest('a[href]');

        if (!link || desktopMedia.matches) return;

        // Ignore special navigation actions.
        if (
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey
        ) {
            return;
        }

        /*
         * Important:
         * Do not call preventDefault().
         * Do not set inert on the clicked link.
         *
         * Allow the browser to navigate normally.
         */

        document.body.classList.remove('admin-sidebar-open');

        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'فتح القائمة الجانبية');
            toggle.setAttribute('title', 'فتح القائمة الجانبية');
        }

        if (backdrop) {
            backdrop.hidden = true;
        }

        sidebar?.setAttribute('aria-hidden', 'true');
    });

    /*
    |--------------------------------------------------------------------------
    | Submenu Accessibility
    |--------------------------------------------------------------------------
    */

    sidebar?.querySelectorAll('[data-sidebar-toggle]').forEach((menuToggle) => {
        const isOpen =
            menuToggle.closest('.nav-item')?.classList.contains('menu-open') ?? false;

        menuToggle.setAttribute('aria-expanded', String(isOpen));
    });

    /*
    |--------------------------------------------------------------------------
    | Escape Key
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape' &&
            !desktopMedia.matches &&
            document.body.classList.contains('admin-sidebar-open')
        ) {
            closeSidebar({ restoreFocus: true });
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Responsive Table Labels
    |--------------------------------------------------------------------------
    */

    const applyResponsiveTableLabels = (scope = document) => {
        const tables = [];

        if (scope instanceof HTMLTableElement) {
            tables.push(scope);
        }

        if (scope instanceof Element) {
            const containingTable = scope.closest('.content-wrapper table');

            if (containingTable) {
                tables.push(containingTable);
            }
        }

        if (scope.querySelectorAll) {
            tables.push(...scope.querySelectorAll('.content-wrapper table'));
        }

        tables.forEach((table) => {
            table.classList.add('admin-responsive-table');

            const headerRow = table.querySelector('thead tr:last-child');

            if (!headerRow) return;

            const labels = Array.from(headerRow.children).map((cell, index) => {
                const label = (
                    cell.innerText ||
                    cell.textContent ||
                    ''
                ).replace(/\s+/g, ' ').trim();

                return label || (index === 0 ? 'اختيار' : 'بيانات');
            });

            table.querySelectorAll('tbody tr').forEach((row) => {
                Array.from(row.children).forEach((cell, index) => {
                    if (
                        cell.tagName !== 'TD' ||
                        cell.hasAttribute('colspan')
                    ) {
                        return;
                    }

                    cell.setAttribute(
                        'data-label',
                        labels[index] || 'بيانات'
                    );
                });
            });
        });
    };

    // Initial table processing.
    applyResponsiveTableLabels();

    /*
    |--------------------------------------------------------------------------
    | Livewire Table Observer
    |--------------------------------------------------------------------------
    */

    const tableObserver = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    applyResponsiveTableLabels(node);
                }
            }
        }
    });

    const content = document.querySelector('.content-wrapper');

    if (content) {
        tableObserver.observe(content, {
            childList: true,
            subtree: true
        });
    }
});
</script>

</body>
</html>
