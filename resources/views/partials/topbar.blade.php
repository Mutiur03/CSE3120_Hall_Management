<header class="topbar">
    <div class="topbar-left">
        <button type="button" class="sidebar-toggle-mobile" id="sidebarToggleMobile" aria-label="Open navigation menu">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                @yield('breadcrumb')
            </ol>
        </nav>
    </div>
    <div class="topbar-right">
        <div class="topbar-actions">
            <div class="dropdown">
                <button type="button" class="btn btn-user" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu">
                    <div class="user-avatar" aria-hidden="true">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info d-none d-md-block">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-role">Administrator</span>
                    </div>
                    <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.change-password') }}">
                            Change Password
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
