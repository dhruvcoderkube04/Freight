<div id="kt_app_sidebar" class="app-sidebar  flex-column " data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">


    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="#">
            <img alt="Logo" src="{{ asset('assets/media/images/logos/default-dark.svg') }}"
                class="h-25px app-sidebar-logo-default">

            <img alt="Logo" src="{{ asset('assets/media/images/logos/default-small.svg') }}"
                class="h-20px app-sidebar-logo-minimize">
        </a>
        <!--end::Logo image-->

        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate "
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">

            <i class="ki-duotone ki-black-left-line fs-3 rotate-180"><span class="path1"></span><span
                    class="path2"></span></i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Logo-->
    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <!--begin::Scroll wrapper-->
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                data-kt-scroll-save-state="true">
                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                    data-kt-menu="true" data-kt-menu-expand="false">

                    <div data-kt-menu-trigger="click" class="menu-item" title="Dashboard" data-bs-toggle="tooltip"
                        data-bs-placement="right">
                        <a class="menu-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-home fs-1 text-white"></i>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>

                    <div data-kt-menu-trigger="click" class="menu-item" title="Customer Orders" data-bs-toggle="tooltip"
                        data-bs-placement="right">
                        <a class="menu-link {{ request()->is(['orders-list/*', 'orders-list', 'orders-list/action']) ? 'active' : '' }}"
                            href="#">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-basket fs-1 text-white">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Customer Orders</span>
                        </a>
                    </div>

                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion"
                        title="Wholesaler" data-bs-toggle="tooltip" data-bs-placement="right">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-briefcase fs-2 text-white">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Wholesalers</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link" href="#">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Wholesaler List</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="#">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Requested Wholesaler</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- CMS Settings --}}
                    <div data-kt-menu-trigger="click" class="menu-item" title="Site Settings" data-bs-toggle="tooltip" data-bs-placement="right">
                        <a class="menu-link {{ request()->is('admin/settings') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-element-7 fs-2 text-white">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Site Settings</span>
                        </a>
                    </div>

                    {{-- Customer List --}}
                    <div data-kt-menu-trigger="click" class="menu-item" title="Customers" data-bs-toggle="tooltip" data-bs-placement="right">
                        <a class="menu-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-address-book fs-2 text-white">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            <span class="menu-title">Customers</span>
                        </a>
                    </div>

                    {{-- payment-requests --}}
                    <div data-kt-menu-trigger="click" class="menu-item" title="Payment Requests" data-bs-toggle="tooltip" data-bs-placement="right">
                        <a class="menu-link {{ request()->is('admin/payment-requests*') ? 'active' : '' }}" href="{{ route('admin.payment-requests.index') }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-text-align-center fs-2 text-white">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                            <span class="menu-title">Payment Requests</span>
                        </a>
                    </div>

                </div>
                <!--end::Menu-->
            </div>
            <!--end::Scroll wrapper-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
<!--end::Sidebar-->
