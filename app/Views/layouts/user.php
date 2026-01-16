<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'User Dashboard' ?> | Attendance System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Glassmorphism Navbar */
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0.8rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            font-weight: 500;
            color: #64748b;
            padding: 0.5rem 1.2rem !important;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 0.2rem;
        }

        .nav-link:hover {
            color: #6366f1;
            background: rgba(99, 102, 241, 0.05);
        }

        .nav-link.active {
            color: #6366f1;
            background: rgba(99, 102, 241, 0.1);
        }

        .main-content {
            flex: 1;
            padding: 2rem 0;
        }

        /* Premium Components */
        .card {
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem 0;
            margin-top: auto;
        }

        .profile-img {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
        }

        /* Badge Styling */
        .badge-custom {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        #custom-alert {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1050;
        }

        .loader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .hidden { display: none; }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/user/home">
                <i class="bi bi-calendar2-check-fill me-2"></i>
                <span>Attendance System</span>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="bi bi-list fs-2"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() == 'user/home' ? 'active' : '' ?>" href="/user/home">
                            <i class="bi bi-grid-1x2-fill me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() == 'user/attendance' ? 'active' : '' ?>" href="/user/attendance">
                            <i class="bi bi-clock-history me-2"></i>Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() == 'user/leaves' ? 'active' : '' ?>" href="/user/leaves">
                            <i class="bi bi-envelope-paper-fill me-2"></i>Leaves
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="me-2 text-end d-none d-sm-block">
                                <div class="fw-bold small lh-1"><?= session()->get('name') ?? 'User' ?></div>
                                <div class="text-muted" style="font-size: 0.7rem;">Employee</div>
                            </div>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name') ?? 'U') ?>&background=6366f1&color=fff" class="profile-img" alt="Profile">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                            <li class="px-3 py-2 border-bottom mb-2 d-sm-none">
                                <div class="fw-bold"><?= session()->get('name') ?? 'User' ?></div>
                                <div class="text-muted small">Employee</div>
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-shield-lock me-2"></i>Security</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="logoutUser()"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container text-center">
            <p class="text-muted small mb-0">&copy; <?= date('Y') ?> Attendance System Inc. All rights reserved.</p>
        </div>
    </footer>

    <div id="custom-alert"></div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/base.js') ?>"></script>
    <script>
        function logoutUser() {
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
    <?= $this->renderSection('scripts') ?>
</body>
</html>
