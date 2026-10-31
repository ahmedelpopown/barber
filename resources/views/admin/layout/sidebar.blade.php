<!-- Main Sidebar Container -->
<aside id="admin-sidebar" class="main-sidebar sidebar-no-expand sidebar-dark-primary elevation-4" aria-label="القائمة الجانبية" aria-hidden="false">

    {{-- ========================================================= --}}
    {{-- Brand --}}
    {{-- ========================================================= --}}

    @php
    $employee = auth('employee')->user();

    $isAdmin = $employee?->hasRole('admin');
    $isEmployee = $employee?->hasRole('employee');
    $isBarber = $employee?->hasRole('barber');
    $brandLogoPath = file_exists(public_path('images/Untitled-4.png'))
        ? 'images/Untitled-4.png'
        : (file_exists(public_path('images/imagelogo.webp'))
            ? 'images/imagelogo.webp'
            : 'images/barber-house-mark.png');
    @endphp

    <a
        href="{{ $isAdmin
            ? route('admin.dashboard')
            : route('employee.dashboard')
        }}"
        class="brand-link">

        <span class="brand-image d-inline-flex align-items-center justify-content-center" aria-hidden="true" style="width:1.8rem;height:1.8rem;margin-right:.25rem;margin-left:.45rem;">
            <img src="{{ asset($brandLogoPath) }}" alt="" style="width:100%;height:100%;object-fit:contain;border-radius:6px;">
        </span>

        <span class="brand-text font-weight-light">
            باربر هاوس
        </span>

    </a>


    {{-- ========================================================= --}}
    {{-- Sidebar --}}
    {{-- ========================================================= --}}

    <div class="sidebar">


        {{-- ========================================================= --}}
        {{-- User Panel --}}
        {{-- ========================================================= --}}

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="image">
                <span class="img-circle elevation-2 d-flex align-items-center justify-content-center bg-secondary text-white font-weight-bold"
                      style="width: 35px; height: 35px;"
                      aria-label="{{ $employee?->name ?? 'المستخدم' }}">
                    {{ mb_substr($employee?->name ?? 'م', 0, 1, 'UTF-8') }}
                </span>
            </div>


            <div class="info">

                <a href="{{ $isAdmin ? route('admin.dashboard') : route('employee.profile.show') }}" class="d-block">
                    {{ $employee?->name ?? 'المستخدم' }}
                </a>


                <small class="text-muted">

                    @if($isAdmin)
                    مدير النظام
                    @elseif($isBarber)
                    حلاق
                    @else
                    موظف
                    @endif

                </small>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- Logout --}}
        {{-- ========================================================= --}}

        <div class="mb-3 px-2 sidebar-logout">

            <form
                action="{{ route('employee.logout') }}"
                method="POST">

                @csrf

                <button
                    type="submit"
                    class="btn btn-danger btn-block">

                    <i class="fas fa-sign-out-alt mr-2"></i>

                    تسجيل الخروج

                </button>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- Search --}}
        {{-- ========================================================= --}}

        <div class="form-inline mb-3">

            <div
                class="input-group"
                data-widget="sidebar-search">

                <input
                    class="form-control form-control-sidebar"
                    type="search"
                    placeholder="بحث"
                    aria-label="Search">

                <div class="input-group-append">

                    <button class="btn btn-sidebar">

                        <i class="fas fa-search fa-fw"></i>

                    </button>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- Sidebar Menu --}}
        {{-- ========================================================= --}}

        <nav class="mt-2">

            <ul
                class="nav nav-pills nav-sidebar flex-column"
                role="menu"
                data-accordion="false">


                {{-- ================================================= --}}
                {{-- ADMIN --}}
                {{-- ================================================= --}}

                @if($isAdmin)

                {{-- Dashboard --}}
                <li class="nav-item">

                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="nav-link {{
                                request()->routeIs('admin.dashboard')
                                    ? 'active'
                                    : ''
                            }}">

                        <i class="nav-icon fas fa-tachometer-alt"></i>

                        <p>
                            الصفحة الرئيسية
                        </p>

                    </a>

                </li>


                {{-- Employees --}}
                <li class="nav-item {{
                        request()->routeIs('admin.employees.*')
                            ? 'menu-open'
                            : ''
                    }}">

                    <button
                        type="button"
                        data-sidebar-toggle
                        aria-controls="admin-employees-submenu"
                        aria-expanded="{{ request()->routeIs('admin.employees.*') ? 'true' : 'false' }}"
                        class="nav-link sidebar-menu-toggle {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-users"></i>

                        <p>
                            الموظفين
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    
                    </button>


                    <ul id="admin-employees-submenu" class="nav nav-treeview">

                        <li class="nav-item">

                            <a
                                href="{{ route('admin.employees.index') }}"
                                class="nav-link {{
                                        request()->routeIs('admin.employees.index')
                                            ? 'active'
                                            : ''
                                    }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    جميع الموظفين
                                </p>

                            </a>

                        </li>


                        <li class="nav-item">

                            <a
                                href="{{ route('admin.employees.create') }}"
                                class="nav-link {{
                                        request()->routeIs('admin.employees.create')
                                            ? 'active'
                                            : ''
                                    }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    إنشاء موظف
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>


                {{-- Customers --}}
                <li class="nav-item {{
                        request()->routeIs('admin.users.*')
                            ? 'menu-open'
                            : ''
                    }}">

                    <button
                        type="button"
                        data-sidebar-toggle
                        aria-controls="admin-users-submenu"
                        aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}"
                        class="nav-link sidebar-menu-toggle {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-user-friends"></i>

                        <p>
                            العملاء
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    
                    </button>


                    <ul id="admin-users-submenu" class="nav nav-treeview">

                        <li class="nav-item">

                            <a
                                href="{{ route('admin.users.index') }}"
                                class="nav-link {{
                                        request()->routeIs('admin.users.index')
                                            ? 'active'
                                            : ''
                                    }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    جميع العملاء
                                </p>

                            </a>

                        </li>


                        <li class="nav-item">

                            <a
                                href="{{ route('admin.users.create') }}"
                                class="nav-link {{
                                        request()->routeIs('admin.users.create')
                                            ? 'active'
                                            : ''
                                    }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    إنشاء عميل
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>


                {{-- Bookings --}}
                <li class="nav-item {{
                        request()->routeIs('admin.bookings.*')
                            ? 'menu-open'
                            : ''
                    }}">

                    <button
                        type="button"
                        data-sidebar-toggle
                        aria-controls="admin-bookings-submenu"
                        aria-expanded="{{ request()->routeIs('admin.bookings.*') ? 'true' : 'false' }}"
                        class="nav-link sidebar-menu-toggle {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-calendar-check"></i>

                        <p>
                            الحجوزات
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    
                    </button>


                    <ul id="admin-bookings-submenu" class="nav nav-treeview">

                        <li class="nav-item">

                            <a
                                href="{{ route('admin.bookings.index') }}"
                                class="nav-link {{
                                        request()->routeIs('admin.bookings.index')
                                            ? 'active'
                                            : ''
                                    }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    جميع الحجوزات
                                </p>

                            </a>

                        </li>


                        <li class="nav-item">

                            <a
                                href="{{ route('admin.bookings.create') }}"
                                class="nav-link {{
                                        request()->routeIs('admin.bookings.create')
                                            ? 'active'
                                            : ''
                                    }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    إنشاء حجز
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>


                {{-- Inventory / Resources --}}
                <li class="nav-item {{ request()->routeIs('admin.resources.*') ? 'menu-open' : '' }}">
                    <a
                        href="{{ route('admin.resources.index') }}"
                        class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>الموارد والمخزون</p>
                    </a>
                </li>

                {{-- Services --}}
                <li class="nav-item {{
                        request()->routeIs('admin.services.*')
                            ? 'menu-open'
                            : ''
                    }}">

                    <button
                        type="button"
                        data-sidebar-toggle
                        aria-controls="admin-services-submenu"
                        aria-expanded="{{ request()->routeIs('admin.services.*') ? 'true' : 'false' }}"
                        class="nav-link sidebar-menu-toggle {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-cut"></i>

                        <p>
                            الخدمات
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    
                    </button>


                    <ul id="admin-services-submenu" class="nav nav-treeview">

                        <li class="nav-item">

                            <a
                                href="{{ route('admin.services.index') }}"
                                class="nav-link {{
                                        request()->routeIs('admin.services.index')
                                            ? 'active'
                                            : ''
                                    }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    جميع الخدمات
                                </p>

                            </a>

                        </li>


                        <li class="nav-item">

                            <a
                                href="{{ route('admin.services.create') }}"
                                class="nav-link {{
                                        request()->routeIs('admin.services.create')
                                            ? 'active'
                                            : ''
                                    }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    إنشاء خدمة
                                </p>

                            </a>

                        </li>

                    </ul>


                </li>

                <li class="nav-item {{ request()->routeIs('admin.expenses.*') ? 'menu-open' : '' }}">
                    <a
                        href="{{ route('admin.expenses.index') }}"
                        class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>المصروفات</p>
                    </a>
                </li>

                @endif


                {{-- ================================================= --}}
                {{-- EMPLOYEE / BARBER --}}
                {{-- ================================================= --}}

                @if($isEmployee || $isBarber)

                {{-- Dashboard --}}
                <li class="nav-item">

                    <a
                        href="{{ route('employee.dashboard') }}"
                        class="nav-link {{
                                request()->routeIs('employee.dashboard')
                                    ? 'active'
                                    : ''
                            }}">

                        <i class="nav-icon fas fa-tachometer-alt"></i>

                        <p>
                            الصفحة الرئيسية
                        </p>

                    </a>

                </li>


                {{-- Inventory / Resources --}}
                <li class="nav-item">
                    <a
                        href="{{ route('employee.resources.index') }}"
                        class="nav-link {{ request()->routeIs('employee.resources.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>الموارد والمخزون</p>
                    </a>
                </li>

                {{-- My Bookings --}}
                <li class="nav-item">

                    <a
                        href="{{ route('employee.bookings.index') }}"
                        class="nav-link {{
                                request()->routeIs('employee.bookings.index')
                                    ? 'active'
                                    : ''
                            }}">

                        <i class="nav-icon fas fa-calendar-check"></i>

                        <p>
                            حجوزاتي
                        </p>

                    </a>

                </li>


                {{-- Create Booking --}}
                <li class="nav-item">

                    <a
                        href="{{ route('employee.bookings.create') }}"
                        class="nav-link {{
                                request()->routeIs('employee.bookings.create')
                                    ? 'active'
                                    : ''
                            }}">

                        <i class="nav-icon fas fa-plus-circle"></i>

                        <p>
                            إنشاء حجز
                        </p>

                    </a>

                </li>


                {{-- Profile --}}
                <li class="nav-item">

                    <a
                        href="{{ route('employee.profile.show') }}"
                        class="nav-link {{
                                request()->routeIs('employee.profile.show')
                                    ? 'active'
                                    : ''
                            }}">

                        <i class="nav-icon fas fa-user"></i>

                        <p>
                            الملف الشخصي
                        </p>

                    </a>

                </li>

                @endif


            </ul>

        </nav>

    </div>

</aside>

<button
    id="adminSidebarBackdrop"
    class="admin-sidebar-backdrop"
    type="button"
    aria-label="إغلاق القائمة الجانبية"
    hidden
></button>