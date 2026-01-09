<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4">
    <!-- Total Users -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stats-card h-100 p-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">TOTAL USERS</span>
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <h2 class="mb-0 fw-bold">124</h2>
            <span class="measurement text-success small"><i class="bi bi-arrow-up-short"></i> 5 new this week</span>
        </div>
    </div>

    <!-- Present Today -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stats-card h-100 p-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">PRESENT TODAY</span>
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
            <h2 class="mb-0 fw-bold">110</h2>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: 88%"></div>
            </div>
            <span class="text-muted small mt-2 d-block">88% Attendance</span>
        </div>
    </div>

    <!-- On Leave -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stats-card h-100 p-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">ON LEAVE</span>
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-calendar-minus-fill"></i>
                </div>
            </div>
            <h2 class="mb-0 fw-bold">8</h2>
            <span class="text-muted small">Pending Approvals: <span class="text-danger fw-bold">3</span></span>
        </div>
    </div>

    <!-- Late Arrivals -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stats-card h-100 p-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">LATE ARRIVALS</span>
                <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-alarm-fill"></i>
                </div>
            </div>
            <h2 class="mb-0 fw-bold">6</h2>
            <span class="text-danger small">Avg delay: 15 mins</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Attendance Chart -->
    <div class="col-12 col-lg-8">
        <div class="stats-card h-100 p-4">
            <h5 class="fw-bold mb-4">Attendance Trends</h5>
            <canvas id="attendanceChart" height="300"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-12 col-lg-4">
        <div class="stats-card h-100 p-4">
            <h5 class="fw-bold mb-4">Recent Activity</h5>
            <div class="activity-feed">
                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-clock text-primary"></i>
                    </div>
                    <div>
                        <p class="mb-0 small fw-semibold">John Doe clocked in</p>
                        <span class="text-muted small">09:05 AM - On Time</span>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                         <i class="bi bi-clock text-primary"></i>
                    </div>
                    <div>
                        <p class="mb-0 small fw-semibold">Jane Smith clocked in</p>
                        <span class="text-muted small">09:45 AM - <span class="text-danger">Late</span></span>
                    </div>
                </div>
                  <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                         <i class="bi bi-file-text text-warning"></i>
                    </div>
                    <div>
                        <p class="mb-0 small fw-semibold">Mike request sick leave</p>
                        <span class="text-muted small">Today</span>
                    </div>
                </div>
            </div>
            <button class="btn btn-light btn-sm w-100 text-primary fw-semibold mt-2">View All Activity</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Updating active state logic for sidebar
    document.querySelector('.page-title').textContent = 'Dashboard Overview';

    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            datasets: [{
                label: 'Present',
                data: [115, 120, 118, 122, 110, 85],
                borderColor: '#0d6efd',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(13, 110, 253, 0.05)'
            },
            {
                label: 'Absent',
                data: [5, 4, 6, 2, 8, 35],
                borderColor: '#dc3545',
                tension: 0.4,
                borderDash: [5, 5]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
<?= $this->endSection() ?>
