<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="row g-4 overflow-hidden">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Attendance History</h3>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm border-0 shadow-sm" style="width: 150px;">
                    <option selected>January 2026</option>
                    <option>December 2025</option>
                    <option>November 2025</option>
                </select>
                <button class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="bi bi-download me-2"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <p class="text-muted small mb-1">Working Days</p>
            <h4 class="fw-bold mb-0">22</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <p class="text-muted small mb-1">Total Present</p>
            <h4 class="fw-bold mb-0 text-success">20</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <p class="text-muted small mb-1">Total Absent</p>
            <h4 class="fw-bold mb-0 text-danger">1</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <p class="text-muted small mb-1">Late Marks</p>
            <h4 class="fw-bold mb-0 text-warning">1</h4>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-semibold small">DATE</th>
                            <th class="py-3 text-muted fw-semibold small">CHECK IN</th>
                            <th class="py-3 text-muted fw-semibold small">CHECK OUT</th>
                            <th class="py-3 text-muted fw-semibold small">WORKING HOURS</th>
                            <th class="py-3 text-muted fw-semibold small">STATUS</th>
                            <th class="py-3 text-muted fw-semibold small pe-4 text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-medium">Jan 14, 2026</td>
                            <td>08:55 AM</td>
                            <td>05:05 PM</td>
                            <td>8h 10m</td>
                            <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">Present</span></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-link btn-sm text-decoration-none p-0"><i class="bi bi-info-circle"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium">Jan 13, 2026</td>
                            <td>09:15 AM</td>
                            <td>05:10 PM</td>
                            <td>7h 55m</td>
                            <td><span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 text-dark">Late</span></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-link btn-sm text-decoration-none p-0"><i class="bi bi-info-circle"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium">Jan 12, 2026</td>
                            <td>-- : --</td>
                            <td>-- : --</td>
                            <td>0h 0m</td>
                            <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3">Absent</span></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-link btn-sm text-decoration-none p-0"><i class="bi bi-info-circle"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3 text-center">
                <button class="btn btn-outline-secondary btn-sm px-4">Load More History</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
