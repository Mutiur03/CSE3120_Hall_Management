<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="fas fa-university"></i>
            <span>Hall MS</span>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
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
                    <i class="fas fa-users"></i>
                    <span>Students</span>
                    @php
                        $studentCount = App\Models\Student::where('status', 'active')->count();
                    @endphp
                    <span class="badge bg-success">{{ $studentCount }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.rooms.index') }}" class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                    <i class="fas fa-door-open"></i>
                    <span>Rooms</span>
                </a>
            </li>

            <li class="nav-item has-submenu">
                <a href="#" class="nav-link {{ request()->routeIs('admin.seats.*') ? 'active' : '' }}">
                    <i class="fas fa-bed"></i>
                    <span>Seat Management</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
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
                    <i class="fas fa-file-alt"></i>
                    <span>Applications</span>
                    @php
                        $pendingAppCount = App\Models\SeatApplication::where('status', 'pending')->count();
                    @endphp
                    @if($pendingAppCount > 0)
                        <span class="badge bg-warning">{{ $pendingAppCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.room-changes.index') }}" class="nav-link {{ request()->routeIs('admin.room-changes.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Room Changes</span>
                    @php
                        $pendingChangeCount = App\Models\RoomChangeRequest::where('status', 'pending')->count();
                    @endphp
                    @if($pendingChangeCount > 0)
                        <span class="badge bg-warning">{{ $pendingChangeCount }}</span>
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
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
