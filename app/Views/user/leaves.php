<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="row g-4 overflow-hidden">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Leave Management</h3>
            <button class="btn btn-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                <i class="bi bi-plus-lg me-2"></i>Apply for Leave
            </button>
        </div>
    </div>

    <!-- Active Leave Requests -->
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="fw-bold mb-0 small text-uppercase tracking-wider">Leave History</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-semibold small">LEAVE TYPE</th>
                            <th class="py-3 text-muted fw-semibold small">FROM - TO</th>
                            <th class="py-3 text-muted fw-semibold small">REASON</th>
                            <th class="py-3 text-muted fw-semibold small">STATUS</th>
                            <th class="py-3 text-muted fw-semibold small text-end pe-4">APPLIED ON</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small mb-1">Full Day Leave</div>
                                <span class="badge bg-primary bg-opacity-10 text-primary extra-small">Medical</span>
                            </td>
                            <td>Jan 20, 2026 - Jan 21, 2026</td>
                            <td class="text-muted small">Doctor's appointment and recovery.</td>
                            <td><span class="badge rounded-pill bg-warning text-dark px-3 mt-1">Pending</span></td>
                            <td class="text-end pe-4 text-muted small">Jan 14, 2026</td>
                        </tr>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small mb-1">Short Leave</div>
                                <span class="badge bg-info bg-opacity-10 text-info extra-small">Personal</span>
                            </td>
                            <td>Jan 10, 2026 (02:00 PM - 05:00 PM)</td>
                            <td class="text-muted small">Early departure for family event.</td>
                            <td><span class="badge rounded-pill bg-success text-white px-3 mt-1">Approved</span></td>
                            <td class="text-end pe-4 text-muted small">Jan 08, 2026</td>
                        </tr>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small mb-1">Half Day</div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary extra-small">Other</span>
                            </td>
                            <td>Jan 05, 2026 (First Half)</td>
                            <td class="text-muted small">Home maintenance work.</td>
                            <td><span class="badge rounded-pill bg-danger text-white px-3 mt-1">Rejected</span></td>
                            <td class="text-end pe-4 text-muted small">Jan 04, 2026</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold mb-0">Apply for Leave</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="applyLeaveForm">
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-muted">Leave Type</label>
                        <select class="form-select border-0 bg-light py-2" id="leaveType">
                            <option value="full">Full Day Leave</option>
                            <option value="half">Half Day Leave</option>
                            <option value="short">Short Leave / Early Exit</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">From Date</label>
                            <input type="date" class="form-control border-0 bg-light py-2">
                        </div>
                        <div id="toDateContainer" class="col-6">
                            <label class="form-label fw-semibold small text-muted">To Date</label>
                            <input type="date" class="form-control border-0 bg-light py-2">
                        </div>
                    </div>

                    <div id="timeContainer" class="row g-3 mb-4 d-none">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">Start Time</label>
                            <input type="time" class="form-control border-0 bg-light py-2">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">End Time</label>
                            <input type="time" class="form-control border-0 bg-light py-2">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-muted">Reason for Leave</label>
                        <textarea class="form-control border-0 bg-light py-2" rows="3" placeholder="Tell us why you need leave..."></textarea>
                    </div>

                    <button type="button" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="border-radius: 12px;">
                        Submit Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .extra-small { font-size: 0.65rem; padding: 0.3em 0.8em; }
    .tracking-wider { letter-spacing: 0.05em; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const leaveType = document.getElementById('leaveType');
        const toDateContainer = document.getElementById('toDateContainer');
        const timeContainer = document.getElementById('timeContainer');

        leaveType.addEventListener('change', function() {
            if (this.value === 'short') {
                toDateContainer.classList.add('d-none');
                timeContainer.classList.remove('d-none');
            } else if (this.value === 'half') {
                toDateContainer.classList.add('d-none');
                timeContainer.classList.add('d-none');
            } else {
                toDateContainer.classList.remove('d-none');
                timeContainer.classList.add('d-none');
            }
        });
    });
</script>
<?= $this->endSection() ?>
