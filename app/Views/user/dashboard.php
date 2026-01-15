<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <!-- Welcome Section -->
    <div class="col-12">
        <div class="d-md-flex align-items-center justify-content-between">
            <div>
                <h3 class="fw-bold mb-1">Welcome back, <?= session()->get('name') ?>! 👋</h3>
                <p class="text-muted mb-0">Here's your attendance overview for today.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex align-items-center bg-white p-2 px-3 rounded-pill shadow-sm border">
                <i class="bi bi-clock-fill text-primary me-2"></i>
                <span id="realTimeClock" class="fw-bold fs-5">00:00:00 AM</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                        <i class="bi bi-percent fs-3"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Attendance Rate</h6>
                        <h3 class="fw-bold mb-0">94.5%</h3>
                    </div>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 94.5%" aria-valuenow="94.5" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 d-flex align-items-center justify-content-around">
                <div class="text-center">
                    <h2 class="fw-bold text-success mb-0">22</h2>
                    <span class="text-muted small">Present Days</span>
                </div>
                <div class="vr mx-3 text-muted opacity-25"></div>
                <div class="text-center">
                    <h2 class="fw-bold text-danger mb-0">2</h2>
                    <span class="text-muted small">Absent Days</span>
                </div>
                <div class="vr mx-3 text-muted opacity-25"></div>
                <div class="text-center">
                    <h2 class="fw-bold text-warning mb-0">1</h2>
                    <span class="text-muted small">Leaves</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <h6 class="text-white text-opacity-75 mb-3">Current Shift Info</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fw-bold mb-1">Morning Shift</h4>
                        <p class="mb-0 small text-white text-opacity-75">
                            <i class="bi bi-calendar3 me-1"></i> Mon - Fri
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-white text-primary mb-1">09:00 AM - 05:00 PM</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Check-in/Out Section -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4">Daily Attendance Portal</h5>
            
            <div class="row align-items-center g-4">
                <div class="col-md-6">
                    <div id="checkInStatus" class="p-4 rounded-4 text-center border bg-light">
                        <div class="status-icon-wrapper mb-3">
                            <i class="bi bi-hand-index-thumb fs-1 text-muted"></i>
                        </div>
                        <h5 id="attendanceStatusTitle" class="fw-bold">Not Checked In</h5>
                        <p class="text-muted small mb-0">You haven't recorded your attendance today.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-grid gap-3">
                        <button id="btnCheckIn" class="btn btn-primary btn-lg py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bi bi-box-arrow-in-right me-2 fs-5"></i>
                            <span class="fw-bold">Check In Now</span>
                        </button>
                        <button id="btnCheckOut" class="btn btn-outline-danger btn-lg py-3 rounded-3 d-flex align-items-center justify-content-center disabled">
                            <i class="bi bi-box-arrow-right me-2 fs-5"></i>
                            <span class="fw-bold">Check Out Now</span>
                        </button>
                    </div>
                </div>
            </div>

            <hr class="my-5 text-muted opacity-25">

            <div class="row text-center g-3">
                <div class="col-4">
                    <p class="text-muted small mb-1">Check-in Time</p>
                    <p id="recordedCheckIn" class="fw-bold mb-0 text-dark">-- : --</p>
                </div>
                <div class="col-4">
                    <p class="text-muted small mb-1">Check-out Time</p>
                    <p id="recordedCheckOut" class="fw-bold mb-0 text-dark">-- : --</p>
                </div>
                <div class="col-4">
                    <p class="text-muted small mb-1">Working Hours</p>
                    <p id="workingHours" class="fw-bold mb-0 text-primary">0h 0m</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Monthly Summary</h5>
                <a href="/user/attendance" class="text-decoration-none small fw-bold">View History</a>
            </div>
            <div class="card-body p-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 py-3 border-0 d-flex align-items-center">
                        <div class="p-2 rounded-2 bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-bold small">On-time Completion</p>
                            <p class="mb-0 text-muted extra-small">18 days this month</p>
                        </div>
                        <span class="badge rounded-pill bg-success small">High</span>
                    </li>
                    <li class="list-group-item px-0 py-3 border-0 d-flex align-items-center">
                        <div class="p-2 rounded-2 bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-bold small">Late Arrivals</p>
                            <p class="mb-0 text-muted extra-small">2 days this month</p>
                        </div>
                        <span class="badge rounded-pill bg-warning text-dark small">Medium</span>
                    </li>
                    <li class="list-group-item px-0 py-3 border-0 d-flex align-items-center">
                        <div class="p-2 rounded-2 bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-bold small">Overtime</p>
                            <p class="mb-0 text-muted extra-small">5.5 hours tracked</p>
                        </div>
                        <span class="badge rounded-pill bg-info text-white small">Low</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .extra-small { font-size: 0.75rem; }
    .status-icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        background: #f8f9fa;
        border-radius: 50%;
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }
    .check-in-active .status-icon-wrapper {
        background: rgba(99, 102, 241, 0.1);
        transform: scale(1.1);
    }
    .check-in-active .bi-hand-index-thumb {
        color: #6366f1 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/user-dashboard.js') ?>"></script>
<?= $this->endSection() ?>
