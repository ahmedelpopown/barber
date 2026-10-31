
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Booking System') }}</title>

    {{-- Vite / Tailwind: load before AdminLTE --}}
    @vite(['resources/css/app.css'])

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    {{-- AdminLTE dependencies --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"
    >

    {{-- Local RTL stylesheet, when available --}}
    @if (file_exists(public_path('dist/css/adminlte.rtl.min.css')))
        <link rel="stylesheet" href="{{ asset('dist/css/adminlte.rtl.min.css') }}">
    @endif

    {{-- Custom overrides must come after framework styles --}}
    <style>
    html,
    body {
        min-height: 100%;
    }

    body {
        min-height: 100vh;
        font-family: 'Cairo', sans-serif;
        direction: rtl;
        text-align: right;
        overflow-x: hidden;
    }

    /* Keep AdminLTE's normal document flow: sidebar is fixed, while
       header, content and footer stay in the page flow. */
    .wrapper {
        position: relative;
        display: flex;
        flex-direction: column;
        min-height: 100vh !important;
        min-height: 100dvh !important;
        width: 100%;
        overflow-x: clip;
    }

    .main-header {
        position: sticky !important;
        top: 0;
        z-index: 1035;
        min-height: 58px;
        margin: 0 !important;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .06);
        transition: margin-right .25s ease;
    }

    .main-header .navbar-nav .nav-link {
        min-height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .admin-sidebar-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 48px;
        min-height: 58px;
        border: 0;
        background: transparent;
        color: #475569;
        cursor: pointer;
    }

    .admin-sidebar-toggle:hover,
    .admin-sidebar-toggle:focus {
        color: #92400e;
        background: #f8fafc;
        outline: none;
    }

    /* Mobile / Tablet Sidebar Drawer */
.main-sidebar {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    left: auto !important;
    bottom: 0 !important;

    width: 270px !important;
    max-width: calc(100vw - 44px);

    height: 100vh;
    height: 100dvh;

    margin: 0 !important;
    overflow: hidden;

    transform: translate3d(105%, 0, 0) !important;
    visibility: hidden;

    transition:
        transform .28s cubic-bezier(.22, 1, .36, 1),
        visibility 0s linear .28s;

    z-index: 1041 !important;
}

.main-sidebar::before {
    top: 0 !important;
    right: 0 !important;
    left: auto !important;
    bottom: 0 !important;

    width: 270px !important;
    height: 100% !important;

    transform: none !important;
}

.main-sidebar .sidebar {
    height: calc(100% - 57px);
    overflow-y: auto;
    overflow-x: hidden;
    padding-bottom: 1rem;

    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
}

body.admin-sidebar-open .main-sidebar {
    transform: translate3d(0, 0, 0) !important;
    visibility: visible;

    transition:
        transform .28s cubic-bezier(.22, 1, .36, 1),
        visibility 0s;
}

body.admin-sidebar-open .main-sidebar::before {
    transform: none !important;
    visibility: visible;
}

.admin-sidebar-backdrop {
    position: fixed;
    inset: 0;

    z-index: 1040;
    width: 100%;
    height: 100%;

    border: 0;
    background: rgba(15, 23, 42, .52);

    cursor: pointer;
    backdrop-filter: blur(2px);
}

.admin-sidebar-backdrop[hidden] {
    display: none !important;
}

body.admin-sidebar-open {
    overflow: hidden;
}

    /* The content area expands naturally; the footer remains after it. */
    .content-wrapper {
        position: relative;
        flex: 1 0 auto;
        min-height: 0 !important;
        margin: 0 !important;
        padding-bottom: 1rem;
        transition: margin-right .25s ease;
    }

    .content-wrapper > .content {
        padding: 1rem;
    }

    .main-footer {
        position: relative;
        flex: 0 0 auto;
        clear: both;
        min-height: 58px;
        margin: 0 !important;
        padding: .85rem 1.25rem;
        text-align: right;
        transition: margin-right .25s ease;
    }

    .nav-sidebar .nav-icon {
        margin-left: .2rem;
        margin-right: 0 !important;
    }

    .preloader {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: flex;
        background: #f4f6f9;
        transition: opacity .25s ease, visibility .25s ease;
        animation: preloader-timeout 0s linear 1.6s forwards;
    }

    .preloader.preloader-hidden {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    @keyframes preloader-timeout {
        to {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
    }

    /* Table wrappers must remain horizontally scrollable when columns exceed the viewport. */
    .content-wrapper .overflow-x-auto,
    .content-wrapper .table-responsive {
        display: block;
        width: 100%;
        max-width: 100%;
        overflow-x: auto !important;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #94a3b8 #e2e8f0;
        overscroll-behavior-x: contain;
    }

    .content-wrapper .overflow-x-auto::-webkit-scrollbar,
    .content-wrapper .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .content-wrapper .overflow-x-auto::-webkit-scrollbar-track,
    .content-wrapper .table-responsive::-webkit-scrollbar-track {
        background: #e2e8f0;
        border-radius: 999px;
    }

    .content-wrapper .overflow-x-auto::-webkit-scrollbar-thumb,
    .content-wrapper .table-responsive::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 999px;
    }

    .content-wrapper table {
        width: 100% !important;
        min-width: max(100%, 900px) !important;
        max-width: none;
        table-layout: auto;
        border-collapse: collapse;
        font-size: .8rem;
    }

    .content-wrapper table th,
    .content-wrapper table td {
        padding: .55rem .45rem !important;
        white-space: normal !important;
        min-width: 0 !important;
        max-width: 100%;
        overflow-wrap: break-word;
        word-break: normal;
        vertical-align: middle;
        line-height: 1.45;
    }

    .content-wrapper table th {
        font-size: .72rem;
        line-height: 1.4;
    }

    /* Keep selection/action columns compact and prevent labels from breaking letter by letter. */
    .content-wrapper table th:first-child,
    .content-wrapper table td:first-child {
        width: 3.25rem;
        white-space: nowrap !important;
        text-align: center;
    }

    .content-wrapper table td .flex,
    .content-wrapper table td .inline-flex {
        flex-wrap: wrap;
    }

    .content-wrapper table td button,
    .content-wrapper table td a {
        max-width: 100%;
    }

    @media (max-width: 767.98px) {
        .main-header {
            min-height: 54px;
        }

        .main-header .navbar-nav .nav-link {
            min-height: 54px;
            padding-left: .65rem;
            padding-right: .65rem;
        }

        .main-header .navbar-search-block,
        .main-header [data-widget="navbar-search"] {
            display: none !important;
        }

        .content-wrapper > .content {
            padding: .75rem;
        }

        .main-footer {
            padding: .75rem 1rem;
            font-size: .8rem;
        }

        /* Mobile tables become compact cards, so every column remains readable. */
        .content-wrapper table.admin-responsive-table {
            display: block;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100%;
            border: 0;
            background: transparent;
        }

        .content-wrapper table.admin-responsive-table thead {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .content-wrapper table.admin-responsive-table tbody {
            display: block;
            width: 100%;
        }

        .content-wrapper table.admin-responsive-table tbody tr {
            display: block;
            width: 100%;
            margin-bottom: .75rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: .85rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        }

        .content-wrapper table.admin-responsive-table tbody td {
            display: grid;
            grid-template-columns: minmax(68px, 30%) minmax(0, 1fr);
            align-items: start;
            gap: .65rem;
            width: 100%;
            padding: .55rem .75rem !important;
            border-top: 1px solid #f1f5f9;
            text-align: right;
            font-size: .78rem;
            overflow-wrap: break-word;
            word-break: normal;
        }

        .content-wrapper table.admin-responsive-table tbody td:first-child {
            border-top: 0;
        }

        .content-wrapper table.admin-responsive-table tbody td::before {
            content: attr(data-label);
            color: #64748b;
            font-size: .7rem;
            font-weight: 700;
            line-height: 1.5;
        }

        .content-wrapper table.admin-responsive-table tbody td[colspan] {
            display: block;
            text-align: center;
        }

        .content-wrapper table.admin-responsive-table tbody td[colspan]::before {
            content: none;
        }

        .content-wrapper table.admin-responsive-table tbody td > * {
            min-width: 0;
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: normal;
        }

        .content-wrapper table.admin-responsive-table tbody td:first-child {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            text-align: right;
        }

        .content-wrapper table.admin-responsive-table tbody td:first-child::before {
            content: attr(data-label);
            margin-left: .65rem;
        }

        .content-wrapper table.admin-responsive-table tbody td .flex {
            justify-content: flex-start;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .main-sidebar {
            transition: none !important;
        }
    }

    .sidebar-menu-toggle {
        display: block;
        width: 100%;
        border: 0;
        background: transparent;
        color: inherit;
        font: inherit;
        text-align: inherit;
        cursor: pointer;
    }

    .sidebar-menu-toggle:focus-visible {
        outline: 2px solid rgba(255, 255, 255, .9);
        outline-offset: -3px;
    }

    /* The submenu is controlled by our own button handler. Do not let
       AdminLTE's delegated treeview handler toggle it a second time. */
    .main-sidebar .nav-sidebar .nav-item > .nav-treeview {
        display: none !important;
    }

    .main-sidebar .nav-sidebar .nav-item.menu-open > .nav-treeview {
        display: block !important;
    }

    .sidebar-menu-toggle .right {
        transition: transform .2s ease;
    }

    .sidebar-menu-toggle[aria-expanded="true"] .right {
        transform: rotate(-90deg);
    }

    /* Desktop: normal AdminLTE layout. Sidebar starts at the very top;
       header, content and footer are aligned beside it, not underneath it. */
    @media (min-width: 992px) {
        .main-sidebar {
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 250px !important;
            min-width: 250px !important;
            max-width: 250px !important;
            height: 100vh !important;
            height: 100dvh !important;
            transform: none !important;
            visibility: visible !important;
             pointer-events: auto;
            transition: width .25s ease !important;
        }

        .main-sidebar::before {
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 250px !important;
            height: 100% !important;
            visibility: visible !important;
        }

        .main-header,
        .content-wrapper,
        .main-footer {
            width: auto !important;
            margin-right: 250px !important;
            margin-left: 0 !important;
        }

        .main-header {
            position: sticky !important;
            top: 0;
        }

        .admin-sidebar-toggle {
            display: inline-flex !important;
        }

        .admin-sidebar-backdrop {
            display: none !important;
        }

        /* Keep the collapsed sidebar in icon-only mode, even on hover. */
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand,
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand:hover,
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand:focus-within {
            width: 4.6rem !important;
            min-width: 4.6rem !important;
            max-width: 4.6rem !important;
        }

        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand::before {
            width: 4.6rem !important;
        }

        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .brand-link {
            width: 4.6rem !important;
            overflow: hidden;
            white-space: nowrap;
        }

        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .brand-text,
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .user-panel .info,
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .sidebar-logout,
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .sidebar .form-inline,
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .nav-sidebar .nav-link p,
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .nav-sidebar .nav-link .right,
        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .nav-sidebar .nav-treeview {
            display: none !important;
        }

        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .user-panel {
            justify-content: center;
            padding-right: .25rem;
            padding-left: .25rem;
        }

        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .nav-sidebar .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: .75rem .5rem;
            white-space: nowrap;
        }

        body.sidebar-mini.sidebar-collapse .main-sidebar.sidebar-no-expand .nav-sidebar .nav-icon {
            margin: 0 !important;
            width: 1.5rem;
            font-size: 1.05rem;
        }

        body.sidebar-mini.sidebar-collapse .main-header,
        body.sidebar-mini.sidebar-collapse .content-wrapper,
        body.sidebar-mini.sidebar-collapse .main-footer {
            width: auto !important;
            margin-right: 4.6rem !important;
            margin-left: 0 !important;
        }
    }

    @media (max-width: 991.98px) {
        .main-header,
        .content-wrapper,
        .main-footer {
            width: 100% !important;
            margin-right: 0 !important;
            margin-left: 0 !important;
        }

        .main-sidebar {
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            height: 100vh !important;
            height: 100dvh !important;
        }

        .content-wrapper {
            min-height: 0 !important;
        }
    }

</style>

    @livewireStyles
    @stack('styles')
</head>
