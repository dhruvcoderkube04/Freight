<!--begin::Header-->
<div id="kt_app_header" class="app-header " data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}"
    data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}"
    data-kt-sticky-animation="false">

    <!--begin::Header container-->
    <div class="app-container  container-fluid d-flex align-items-stretch justify-content-between "
        id="kt_app_header_container">

        <!--begin::Sidebar mobile toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1"><span class="path1"></span><span
                        class="path2"></span></i>
            </div>
        </div>
        <!--end::Sidebar mobile toggle-->

        <!--begin::Mobile logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <div class="d-none d-lg-block">
                <div class="title-wrapper"><span class="co-name title">{{ config('app.name') }}</span><span class="co-role sub-title"></span></div>
            </div>
            <a href="#" class="d-lg-none d-flex align-items-center">
                @php
                    $userDetail = Auth::user()->userDetail;
                    $logoUrl =
                        $userDetail && $userDetail->company_logo
                        ? Storage::url($userDetail->company_logo)
                        : asset('assets/media/avatars/no-profile.png');
                @endphp
                <img src="{{ $logoUrl }}"
                    onerror="this.onerror=null;this.src='{{ asset('assets/media/avatars/no-profile.png') }}';"
                    class="rounded-3 me-1" alt="user" height="20" />
                {{ Auth::user()->firstname }}
            </a>
        </div>

        <!--begin::Header wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">

            <!--begin::Menu wrapper-->
            <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
                data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
                data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end"
                data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
                data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
                data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                <!--begin::Menu-->
                <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
                    id="kt_app_header_menu" data-kt-menu="true">
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Menu wrapper-->


            <!--begin::Navbar-->
            <div class="app-navbar flex-shrink-0">
                <div class="header-sticky-footer">
                    <div class="app-navbar-item ms-1 ms-md-4">
                        <a href="" class="btn btn-dark btn-active-dark">
                            <i class="ki-duotone ki-truck text-inverse-primary fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            Track Your Order
                        </a>
                    </div>

                </div>

                <!--begin::Notifications-->
                <div class="app-navbar-item ms-1 ms-md-4">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative"
                        data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end"
                        id="kt_menu_item_wow">
                        <i class="ki-duotone ki-notification-status fs-2"><span class="path1"></span><span
                            class="path2"></span><span class="path3"></span><span
                            class="path4"></span></i>


                        <span id="notification-count"
                            class="position-absolute badge bg-danger text-white fw-bold justify-content-center" style="
                                    top: -6px;
                                    right: -3px;
                                    width: 20px;
                                    height: 20px;
                                    border-radius: 50%;
                                    font-size: 9px;
                                    text-align: center;
                                    ">
                            0
                        </span>
                        <!--end::Bullet-->
                    </div>
                    <!--end::Menu wrapper-->

                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true"
                        id="kt_menu_notifications">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="kt_topbar_notifications_3" role="tabpanel">
                                <div class="scroll-y mh-325px my-5 px-4" id="notification-list">
                                    <!-- Dynamic notifications will appear here -->
                                    <div class="text-muted text-center py-10">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Notifications-->

                <!--begin::Theme mode-->
                <div class="app-navbar-item ms-1 ms-md-4">
                    <!--begin::Menu toggle-->
                    <a href="#"
                        class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px"
                        data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-night-day theme-light-show fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                            <span class="path7"></span>
                            <span class="path8"></span>
                            <span class="path9"></span>
                            <span class="path10"></span>
                        </i>
                        <i class="ki-duotone ki-moon theme-dark-show fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <!--begin::Menu toggle-->
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                        data-kt-menu="true" data-kt-element="theme-mode-menu">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-night-day fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                        <span class="path9"></span>
                                        <span class="path10"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Light</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-moon fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Dark</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-screen fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <span class="menu-title">System</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Theme mode-->
                <!--begin::User menu-->
                <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle"
                    data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                    data-kt-menu-placement="bottom-end">
                    <!--begin::Menu wrapper-->
                    <div class="cursor-pointer symbol symbol-35px">
                        <img src="{{ $logoUrl }}" class="rounded-3" alt="user" />
                    </div>
                </div>
                <!--end::User menu-->
                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                    data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <img src="{{ $logoUrl }}" class="rounded-3" alt="user" />
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">
                                    {{ Auth::user()->firstname }}
                                    <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"></span>
                                </div>
                                <a href="#"
                                    class="fw-semibold text-muted text-hover-primary fs-7">{{ Auth::user()->firstname }}
                                    {{ Auth::user()->lastname }}</a>
                            </div>
                            <!--end::Username-->
                        </div>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu separator-->
                    <div class="separator my-2"></div>
                    <!--end::Menu separator-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="#" class="menu-link px-5">My Profile</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="menu-link px-5 border-0 bg-transparent text-hover-danger">
                                <span class="menu-icon">
                                    <i class="fas fa-sign-out-alt fs-2"></i>
                                </span>
                                <span class="menu-title">Sign Out</span>
                            </button>
                        </form>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::User account menu-->
                <!--end::Menu wrapper-->

            </div>
            <!--end::Navbar-->

        </div>
        <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
</div>
<!--end::Header-->
