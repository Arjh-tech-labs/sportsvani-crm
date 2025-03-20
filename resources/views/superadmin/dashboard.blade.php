<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - SportsVani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            width: 250px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.5rem 1rem;
            margin: 0.2rem 0;
            border-radius: 0.25rem;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: #4a6cf7;
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        .stat-card {
            border-left: 4px solid;
        }
        .stat-card.users {
            border-left-color: #4a6cf7;
        }
        .stat-card.teams {
            border-left-color: #28a745;
        }
        .stat-card.tournaments {
            border-left-color: #ffc107;
        }
        .stat-card.matches {
            border-left-color: #dc3545;
        }
        .stat-icon {
            font-size: 2rem;
            opacity: 0.8;
        }
        .stat-card .stat-icon.users {
            color: #4a6cf7;
        }
        .stat-card .stat-icon.teams {
            color: #28a745;
        }
        .stat-card .stat-icon.tournaments {
            color: #ffc107;
        }
        .stat-card .stat-icon.matches {
            color: #dc3545;
        }
        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        .activity-icon.user {
            background-color: rgba(74, 108, 247, 0.1);
            color: #4a6cf7;
        }
        .activity-icon.team {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3">
            <h3>SportsVani</h3>
            <p class="text-muted">Super Admin Panel</p>
        </div>
        <hr class="my-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('superadmin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('superadmin.users') }}">
                    <i class="bi bi-people"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('superadmin.teams') }}">
                    <i class="bi bi-shield"></i> Teams
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('superadmin.tournaments') }}">
                    <i class="bi bi-trophy"></i> Tournaments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('superadmin.matches') }}">
                    <i class="bi bi-calendar-event"></i> Matches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('superadmin.system') }}">
                    <i class="bi bi-gear"></i> System
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('superadmin.settings') }}">
                    <i class="bi bi-sliders"></i> Settings
                </a>
            </li>
        </ul>
        <hr class="my-2">
        <div class="p-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard</h2>
            <div>
                <span class="me-2">Welcome, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card stat-card users">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted">Total Users</h6>
                            <h3 class="card-title mb-0">{{ $stats['users'] }}</h3>
                        </div>
                        <div class="stat-icon users">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card teams">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted">Teams</h6>
                            <h3 class="card-title mb-0">{{ $stats['teams'] }}</h3>
                        </div>
                        <div class="stat-icon teams">
                            <i class="bi bi-shield"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card tournaments">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted">Tournaments</h6>
                            <h3 class="card-title mb-0">{{ $stats['tournaments'] }}</h3>
                        </div>
                        <div class="stat-icon tournaments">
                            <i class="bi bi-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card matches">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted">Matches</h6>
                            <h3 class="card-title mb-0">{{ $stats['matches'] }}</h3>
                        </div>
                        <div class="stat-icon matches">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Activity -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body p-0">
                        @if(count($recentUsers) == 0 && count($recentTeams) == 0)
                            <div class="p-3 text-center">
                                <p class="text-muted">No recent activity</p>
                            </div>
                        @else
                            @foreach($recentUsers as $user)
                                <div class="activity-item d-flex align-items-start">
                                    <div class="activity-icon user">
                                        <i class="bi bi-person-plus"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">New User Registration</h6>
                                        <p class="mb-1">{{ $user->name }} registered as {{ $user->roles->pluck('name')->implode(', ') }}</p>
                                        <small class="text-muted">{{ $user->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                            
                            @foreach($recentTeams as $team)
                                <div class="activity-item d-flex align-items-start">
                                    <div class="activity-icon team">
                                        <i class="bi bi-shield-plus"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">New Team Created</h6>
                                        <p class="mb-1">{{ $team->name }} team was created by {{ $team->captain->name }}</p>
                                        <small class="text-muted">{{ $team->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary w-100">
                                    <i class="bi bi-person-plus me-2"></i> Add User
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('superadmin.teams.create') }}" class="btn btn-success w-100">
                                    <i class="bi bi-shield-plus me-2"></i> Add Team
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('superadmin.tournaments.create') }}" class="btn btn-warning w-100">
                                    <i class="bi bi-trophy me-2"></i> Add Tournament
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('superadmin.matches.create') }}" class="btn btn-danger w-100">
                                    <i class="bi bi-calendar-plus me-2"></i> Schedule Match
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Database</span>
                            <span class="badge bg-success">Operational</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Firebase Auth</span>
                            <span class="badge bg-success">Operational</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>YouTube API</span>
                            <span class="badge bg-success">Operational</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Storage</span>
                            <span class="badge bg-success">Operational</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

