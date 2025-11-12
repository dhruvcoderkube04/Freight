<header>
    <nav class="ark__navbar">
        <a href="{{ route('quotes.index') }}" class="ark__logo">
            <p>Ark<span>Anu</span></p>
        </a>
        
        <div class="ark__nav-links">
            <div class="ark__search-box ark__mobile--searchbox">
                <span class="ark__search--icon">
                    <img src="{{ asset('assets/images/Search-icon.svg') }}" alt="Search">
                </span>
                <input type="text" placeholder="Search...">
            </div>
            <a href="#dashboard" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="#warehouse" class="{{ request()->routeIs('warehouse.*') ? 'active' : '' }}">Book Warehouse</a>
            <a href="{{ route('quotes.index')}}" class="{{ request()->routeIs('quotes.index') ? 'active' : '' }}">Get A Quote</a>
            <a href="{{ route('quotes.approved') }}" class="{{ request()->routeIs('quotes.approved') ? 'active' : '' }}">Approved Bookings</a>
        </div>
        
        <div class="ark__nav-right">
            <!-- Search Box -->
            <div class="ark__search-box">
                <span class="ark__search--icon">
                    <img src="{{ asset('assets/images/Search-icon.svg') }}" alt="Search">
                </span>
                <input type="text" placeholder="Search...">
            </div>
            
            <!-- Country Select -->
            <div class="ark__dropdown-container" id="arkDropdown">
                <button class="ark__dropdown-button">
                    <div class="ark__flag">
                        <img src="https://flagcdn.com/us.svg" alt="US Flag">
                    </div>
                    <div class="ark__arrow">
                        <img src="{{ asset('assets/images/down-arrow.svg') }}" alt="">
                    </div>
                </button>
                <div class="ark__dropdown-menu">
                    <div class="ark__dropdown-item">
                        <div class="ark__flag">
                            <img src="https://flagcdn.com/us.svg" alt="US Flag">
                        </div>
                        <span class="ark__country-name">United States</span>
                    </div>
                    <div class="ark__dropdown-item">
                        <div class="ark__flag">
                            <img src="https://flagcdn.com/gb.svg" alt="UK Flag">
                        </div>
                        <span class="ark__country-name">United Kingdom</span>
                    </div>
                </div>
            </div>
            
            <!-- Notification -->
            <div class="ark__notification-container" id="arkNotification">
                <div class="ark__notification-button">
                    <img src="{{ asset('assets/images/Notification-icon.svg') }}" alt="">
                    <span class="ark__notification-badge" id="arkNotificationBadge"></span>
                </div>
                <div class="ark__notification-panel">
                    <div class="ark__notification-header">
                        <span class="ark__notification-title">Notifications</span>
                        <button class="ark__notification-clear" id="arkClearNotifications">Clear all</button>
                    </div>
                    <div id="arkNotificationContent"></div>
                </div>
            </div>
            
            <!-- Profile -->
            <div class="ark__profile-container">
                <button class="ark__profile-button" id="arkProfileBtn">
                    <img src="{{ Auth::user()->avatar ?? asset('assets/images/no-profile.png') }}" alt="Profile" class="ark__profile-image">
                    <img src="{{ asset('assets/images/down-arrow.svg') }}" alt="">
                </button>

                <div class="ark__profile-menu" id="arkProfileMenu">
                    <div class="ark__profile-header"> 
                        <img src="{{ Auth::user()->avatar ?? asset('assets/images/no-profile.png') }}" alt="Profile" class="ark__profile-image">
                        <h3>{{ Auth::user()->fullname }}</h3>
                        <p class="mb-0">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="ark__profile-items">
                        <a href="#" class="ark__profile-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>My Profile</span>
                        </a>
                        <a href="#" class="ark__profile-item">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.4625 4.90299L16.0512 4.18915C15.7401 3.64929 15.5845 3.37936 15.3199 3.27172C15.0553 3.16409 14.7559 3.24902 14.1573 3.41889L13.1405 3.70531C12.7583 3.79344 12.3573 3.74344 12.0084 3.56415L11.7277 3.40217C11.4284 3.21052 11.1983 2.92794 11.0708 2.59578L10.7926 1.76462C10.6096 1.2146 10.5181 0.939596 10.3003 0.782298C10.0825 0.625 9.79314 0.625 9.21451 0.625H8.28553C7.7069 0.625 7.41758 0.625 7.19977 0.782298C6.98196 0.939596 6.89047 1.2146 6.70749 1.76462L6.42919 2.59578C6.30179 2.92794 6.07163 3.21052 5.77239 3.40217L5.49166 3.56415C5.1427 3.74344 4.74174 3.79344 4.35957 3.70531L3.3427 3.41889C2.74409 3.24902 2.44479 3.16409 2.18014 3.27172C1.9155 3.37936 1.75996 3.64929 1.44888 4.18915L1.03755 4.90299C0.745964 5.40903 0.600169 5.66205 0.628465 5.9314C0.656762 6.20075 0.851941 6.4178 1.2423 6.85192L2.1015 7.81251C2.3115 8.07836 2.46059 8.54167 2.46059 8.9582C2.46059 9.375 2.31155 9.83818 2.10152 10.1041L1.2423 11.0647C0.851944 11.4988 0.656763 11.7159 0.628466 11.9852C0.600169 12.2546 0.745968 12.5076 1.03756 13.0136L1.44887 13.7274C1.75994 14.2673 1.91549 14.5372 2.18014 14.6449C2.44479 14.7525 2.7441 14.6676 3.34272 14.4977L4.35953 14.2113C4.74177 14.1231 5.14281 14.1731 5.49181 14.3525L5.7725 14.5145C6.07167 14.7061 6.30179 14.9887 6.42917 15.3208L6.70749 16.152C6.89047 16.7021 6.98196 16.9771 7.19977 17.1344C7.41758 17.2917 7.7069 17.2917 8.28553 17.2917H9.21451C9.79314 17.2917 10.0825 17.2917 10.3003 17.1344C10.5181 16.9771 10.6096 16.7021 10.7926 16.152L11.0709 15.3208C11.1983 14.9887 11.4284 14.7061 11.7275 14.5145L12.0082 14.3525C12.3572 14.1731 12.7583 14.1231 13.1405 14.2113L14.1573 14.4977C14.7559 14.6676 15.0553 14.7525 15.3199 14.6449C15.5845 14.5372 15.7401 14.2673 16.0512 13.7274L16.4625 13.0136L16.4625 13.0136C16.7541 12.5076 16.8999 12.2545 16.8716 11.9852C16.8433 11.7159 16.6481 11.4988 16.2577 11.0647L15.3985 10.1041C15.1885 9.83818 15.0395 9.375 15.0395 8.9582C15.0395 8.54167 15.1885 8.07836 15.3985 7.81251L16.2577 6.85192C16.6481 6.4178 16.8433 6.20075 16.8716 5.9314C16.8999 5.66205 16.7541 5.40903 16.4625 4.90299Z" stroke="#747474" stroke-width="1.25" stroke-linecap="round"/>
                                <path d="M11.6341 8.95573C11.6341 10.5666 10.3283 11.8724 8.71745 11.8724C7.10662 11.8724 5.80078 10.5666 5.80078 8.95573C5.80078 7.3449 7.10662 6.03906 8.71745 6.03906C10.3283 6.03906 11.6341 7.3449 11.6341 8.95573Z" stroke="#747474" stroke-width="1.25"/>
                            </svg>
                            <span>Settings</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <a href="javascript:void(0)" 
                            onclick="document.getElementById('logout-form').submit();" 
                            class="ark__profile-item ark__logout"
                            style="cursor: pointer;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>Log Out</span>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            
            <button class="menu ark__menu-toggle">
                <svg viewBox="0 0 64 48">
                    <path d="M19,15 L45,15 C70,15 58,-2 49.0177126,7 L19,37"></path>
                    <path d="M19,24 L45,24 C61.2371586,24 57,49 41,33 L32,24"></path>
                    <path d="M45,33 L19,33 C-8,33 6,-2 22,14 L45,37"></path>
                </svg>
            </button>
        </div>
    </nav>
</header>