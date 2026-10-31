@php
    $employee = auth('employee')->user();
    $isAdmin = $employee?->hasRole('admin') ?? false;

    // The only implemented navbar search is the customer search on today's bookings page.
    $bookingsIndexRoute = $isAdmin ? 'admin.bookings.index' : 'employee.bookings.index';
    $employeeName = $employee?->name ?? 'المستخدم';
    $employeeInitial = mb_substr($employeeName, 0, 1, 'UTF-8');
@endphp

<!-- Navbar: utility actions only; navigation links remain in the sidebar. -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left: sidebar toggle only -->
    <ul class="navbar-nav">
        <li class="nav-item">
          <button
    id="adminSidebarToggle"
    class="nav-link admin-sidebar-toggle"
    type="button"
    aria-controls="admin-sidebar"
    aria-expanded="false"
    aria-label="فتح القائمة الجانبية"
    title="القائمة الرئيسية"
>
    <i class="fas fa-bars" aria-hidden="true"></i>
</button>
        </li>
    </ul>

    <!-- Right: search and personal utilities. No sidebar links are duplicated here. -->
    <ul class="navbar-nav ml-auto align-items-center">
        <!-- Search existing bookings/customer records -->
        <li class="nav-item">
            <a class="nav-link"
               data-widget="navbar-search"
               href="#"
               role="button"
               aria-label="البحث في حجوزات اليوم"
               title="البحث في حجوزات اليوم">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline" action="{{ route($bookingsIndexRoute) }}" method="GET">
                    <div class="input-group input-group-sm w-100">
                        <input
                            class="form-control form-control-navbar"
                            type="search"
                            name="search"
                            value="{{ request()->routeIs('admin.bookings.*', 'employee.bookings.*') ? request('search') : '' }}"
                            placeholder="ابحث باسم العميل أو البريد أو رقم الهاتف..."
                            aria-label="ابحث عن عميل في حجوزات اليوم"
                            autocomplete="off"
                        >
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit" aria-label="تنفيذ البحث" title="بحث">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar"
                                    type="button"
                                    data-widget="navbar-search"
                                    aria-label="إغلاق البحث"
                                    title="إغلاق">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Fullscreen utility -->
        <li class="nav-item">
            <a class="nav-link"
               data-widget="fullscreen"
               href="#"
               role="button"
               aria-label="ملء الشاشة"
               title="ملء الشاشة">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <!-- User menu: identity, employee profile (when available), and logout -->
        <li class="nav-item dropdown">
            <a class="nav-link"
               data-toggle="dropdown"
               href="#"
               role="button"
               aria-label="قائمة حساب المستخدم"
               title="حساب المستخدم">
                <span class="img-circle elevation-1 d-inline-flex align-items-center justify-content-center bg-secondary text-white font-weight-bold"
                      style="width: 28px; height: 28px; font-size: 13px;"
                      aria-hidden="true">{{ $employeeInitial }}</span>
                <span class="d-none d-md-inline ml-1">{{ $employeeName }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-item-text text-center py-3">
                    <span class="img-circle mb-2 mx-auto d-flex align-items-center justify-content-center bg-secondary text-white font-weight-bold"
                          style="width: 52px; height: 52px; font-size: 22px;"
                          aria-hidden="true">{{ $employeeInitial }}</span>
                    <div class="font-weight-bold">{{ $employeeName }}</div>
                    <small class="text-muted">
                        @if ($isAdmin)
                            مدير النظام
                        @elseif ($employee?->hasRole('barber'))
                            حلاق
                        @else
                            موظف
                        @endif
                    </small>
                </div>

                @unless ($isAdmin)
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('employee.profile.show') }}" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> ملفي الشخصي
                    </a>
                @endunless

                <div class="dropdown-divider"></div>
                <form action="{{ route('employee.logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> تسجيل الخروج
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
