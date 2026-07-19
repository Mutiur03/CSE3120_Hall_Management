<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="fas fa-university"></i>
            <span>Hall MS</span>
        </a>
        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Collapse sidebar">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-header">Management</li>

            <li class="nav-item">
                <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <i class="fas fa-users" aria-hidden="true"></i>
                    <span>Students</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.rooms.index') }}" class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                    <i class="fas fa-door-open" aria-hidden="true"></i>
                    <span>Rooms</span>
                </a>
            </li>

            <li class="nav-item has-submenu">
                <a href="#" class="nav-link {{ request()->routeIs('admin.seats.*') ? 'active' : '' }}">
                    <i class="fas fa-bed" aria-hidden="true"></i>
                    <span>Seat Management</span>
                    <i class="fas fa-chevron-right submenu-icon" aria-hidden="true"></i>
                </a>
                <ul class="submenu {{ request()->routeIs('admin.seats.*') ? 'show' : '' }}">
                    <li><a href="{{ route('admin.seats.index') }}" class="submenu-link {{ request()->routeIs('admin.seats.index') ? 'active' : '' }}">All Seats</a></li>
                    <li><a href="{{ route('admin.seats.available') }}" class="submenu-link {{ request()->routeIs('admin.seats.available') ? 'active' : '' }}">Available Seats</a></li>
                    <li><a href="{{ route('admin.seats.occupied') }}" class="submenu-link {{ request()->routeIs('admin.seats.occupied') ? 'active' : '' }}">Occupied Seats</a></li>
                    <li><a href="{{ route('admin.seats.statistics') }}" class="submenu-link {{ request()->routeIs('admin.seats.statistics') ? 'active' : '' }}">Statistics</a></li>
                    <li><a href="{{ route('admin.seats.allocate-form') }}" class="submenu-link {{ request()->routeIs('admin.seats.allocate-form') ? 'active' : '' }}">Allocate Seat</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                    <span>Applications</span>
                    @php
                        $appCount = App\Models\SeatApplication::count();
                    @endphp
                    @if($appCount > 0)
                        <span class="badge nav-badge">{{ $appCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.room-changes.index') }}" class="nav-link {{ request()->routeIs('admin.room-changes.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                    <span>Room Changes</span>
                    @php
                        $changeCount = App\Models\RoomChangeRequest::count();
                    @endphp
                    @if($changeCount > 0)
                        <span class="badge nav-badge">{{ $changeCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.dining.index') }}" class="nav-link {{ request()->routeIs('admin.dining.*') ? 'active' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span>Dining</span>
                </a>
            </li>

            <li class="nav-header">Reports & Settings</li>

            <li class="nav-item has-submenu">
                <a href="#" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </a>
                <ul class="submenu {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}">
                    <li><a href="{{ route('admin.reports.overview') }}" class="submenu-link {{ request()->routeIs('admin.reports.overview') ? 'active' : '' }}">Overview</a></li>
                    <li><a href="{{ route('admin.reports.students') }}" class="submenu-link {{ request()->routeIs('admin.reports.students') ? 'active' : '' }}">Students</a></li>
                    <li><a href="{{ route('admin.reports.room-occupancy') }}" class="submenu-link {{ request()->routeIs('admin.reports.room-occupancy') ? 'active' : '' }}">Room Occupancy</a></li>
                    <li><a href="{{ route('admin.reports.dining') }}" class="submenu-link {{ request()->routeIs('admin.reports.dining') ? 'active' : '' }}">Dining</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn btn-logout">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</aside>
