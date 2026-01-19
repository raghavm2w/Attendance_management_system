<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<style>
    .sortable {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.2s;
    }
    .sortable:hover {
        background-color: #e9ecef !important;
    }
    .sortable .sort-icon {
        font-size: 0.8rem;
        color: #adb5bd;
    }
    .sortable.active .sort-icon {
        color: #0d6efd !important;
    }
    .extra-small {
        font-size: 0.75rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold">Leave Requests</h4>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Filters Section -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted text-uppercase">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="searchInput" placeholder="User, email, type, reason...">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted text-uppercase">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="pending" selected>Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted text-uppercase">Applied On</label>
                <div class="d-flex gap-2">
                    <input type="date" class="form-control form-control-sm" id="applied_from">
                    <input type="date" class="form-control form-control-sm" id="applied_to">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted text-uppercase">On Leave </label>
                <div class="d-flex gap-2">
                    <input type="date" class="form-control form-control-sm" id="leave_from">
                    <input type="date" class="form-control form-control-sm" id="leave_to">
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-outline-secondary btn-sm w-100" id="btnResetFilters" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="leavesTable">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 sortable active" data-sort="user">
                            User <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="type">
                            Type <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="start_date">
                            Dates <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                        </th>
                        <th class="border-0">Reason</th>
                        <th class="border-0 sortable" data-sort="status">
                            Status <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="created_at">
                            Applied On <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                        </th>
                        <th class="border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="leavesTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Loading leave requests...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small" id="paginationInfo">
                Showing 0-0 of 0
            </div>
            <nav aria-label="Leaves pagination">
                <ul class="pagination pagination-sm mb-0" id="paginationControls">
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Reject Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="rejectLeaveId">
                <p class="text-muted mb-0">Are you sure you want to reject this leave request?</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmRejectBtn">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approveLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-success">Approve Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="approveLeaveId">
                <p class="text-muted mb-0">Are you sure you want to approve this leave request?</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" id="confirmApproveBtn">Approve</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-leaves.js') ?>"></script>
<?= $this->endSection() ?>

