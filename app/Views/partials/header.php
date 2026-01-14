<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'Attendance System') ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

<header class="navbar bg-white border-bottom px-4">
    <div class="container-fluid">
        <span class="navbar-brand fw-semibold">
            <i class="bi bi-calendar2-check-fill me-2"></i>
            Attendance System
        </span>
    <nav class="d-flex ">
        <a href="#" class="text-decoration-none text-dark">
           
        </a>
    </nav>
    </div>
</header>


</body>
</html>