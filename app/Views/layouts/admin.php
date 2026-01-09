<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'Admin Panel' ?> | Attendance System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-width: 250px;
            --topbar-height: 60px;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #ffffff;
            border-right: 1px solid #e9ecef;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
            border-bottom: 1px solid #e9ecef;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .nav-link {
            padding: 0.75rem 1.5rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: #f8f9fa;
            border-right: 3px solid var(--primary-color);
        }

        .nav-link i {
            font-size: 1.1rem;
        }

        /* Main Content Styling */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
        }

        /* Topbar Styling */
        .topbar {
            height: var(--topbar-height);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 999;
        }

        .profile-dropdown .dropdown-toggle::after {
            display: none;
        }

        /* Card Styling */
        .stats-card {
            background: #fff;
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .loader {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.hidden {
  display: none;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #ccc;
  border-top-color: #000;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shield-lock-fill me-2"></i> AdminPanel
        </div>
        <div class="sidebar-menu">
            <a href="/admin/dash" class="nav-link <?= uri_string() == 'admin/dash' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="/admin/users" class="nav-link <?= strpos(uri_string(), 'admin/users') !== false ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Users
            </a>
            <a href="/admin/attendance" class="nav-link <?= strpos(uri_string(), 'admin/attendance') !== false ? 'active' : '' ?>">
                <i class="bi bi-calendar-check"></i> Attendance
            </a>
            <a href="/admin/leaves" class="nav-link <?= strpos(uri_string(), 'admin/leaves') !== false ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i> Leaves
            </a>
            <a href="/admin/shifts" class="nav-link <?= strpos(uri_string(), 'admin/shifts') !== false ? 'active' : '' ?>">
                <i class="bi bi-clock"></i> Shifts
            </a>
            <a href="/admin/reports" class="nav-link <?= strpos(uri_string(), 'admin/reports') !== false ? 'active' : '' ?>">
                <i class="bi bi-graph-up"></i> Reports
            </a>
        </div>
    </nav>

    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 text-dark fw-semibold page-title">Dashboard</h5>
        </div>
        <div class="dropdown profile-dropdown">
            <button class="btn btn-link text-dark text-decoration-none dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                <div class="d-flex flex-column align-items-end d-none d-sm-flex">
                    <span class="fw-semibold small">Administrator</span>
                    <span class="text-muted" style="font-size: 0.75rem;">Admin</span>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="rounded-circle" width="35" height="35" alt="Profile">
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2">
                <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                     <a class="dropdown-item py-2 text-danger" href="#" onclick="logout()">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid p-4">
            <?= $this->renderSection('content') ?>
        </div>
    </main>
<div id="custom-alert" class="alert-box"></div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function logout() {
            if(confirm('Are you sure you want to logout?')) {
                fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content


                    }
                }).then(response => {
                    if(response.ok) {
                        window.location.href = '/login';
                    }
                });
            }
        }
    </script>
    <script src="<?= base_url('assets/js/base.js') ?>"> 
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
