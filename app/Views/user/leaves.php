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

    <!-- Leave History -->
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <h5 class="fw-bold mb-0 small text-uppercase tracking-wider">Leave History</h5>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group input-group-sm" style="width: 220px;">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="searchInput" placeholder="Search leaves...">
                        </div>
                        <select class="form-select form-select-sm bg-light border-0" id="statusFilter" style="width: 140px;">
                            <option value="pending" selected>Pending</option>
                            <option value="">All Status</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-semibold small">LEAVE TYPE</th>
                            <th class="py-3 text-muted fw-semibold small">DURATION</th>
                            <th class="py-3 text-muted fw-semibold small">DATE RANGE</th>
                            <th class="py-3 text-muted fw-semibold small">REASON</th>
                            <th class="py-3 text-muted fw-semibold small">STATUS</th>
                            <th class="py-3 text-muted fw-semibold small text-end pe-4 sortable" id="sortAppliedOn" style="cursor: pointer;">
                                APPLIED ON <i class="bi bi-arrow-down ms-1 sort-icon"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="leavesTableBody">
                        <!-- Dynamic content -->
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top d-flex align-items-center justify-content-between py-3 px-4">
                <div class="text-muted small" id="paginationInfo">Showing 0-0 of 0</div>
                <nav aria-label="Leaves pagination">
                    <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                </nav>
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

                        </select>
                    </div>
                     <div class="mb-4">
                        <label class="form-label fw-semibold small text-muted">Leave Duration</label>
                        <select class="form-select border-0 bg-light py-2" id="leaveDuration">
                            <option value="full">Full Day Leave</option>
                            <option value="half">Half Day Leave</option>
                            <!-- <option value="short">Short Leave / Early Exit</option> -->
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">From Date</label>
                            <input type="date" class="form-control border-0 bg-light py-2" min=<?= date('Y-m-d') ?>>
                        </div>
                        <div id="toDateContainer" class="col-6">
                            <label class="form-label fw-semibold small text-muted">To Date</label>
                            <input type="date" class="form-control border-0 bg-light py-2" min=<?= date('Y-m-d') ?>>
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
    .sortable:hover { background-color: #e9ecef; }
    .sort-icon { font-size: 0.75rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/user-leaves.js') ?>"></script>

<?= $this->endSection() ?>
