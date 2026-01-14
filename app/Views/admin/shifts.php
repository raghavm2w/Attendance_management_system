<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<style>
    .sortable {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.2s;
    }
    .sortable:hover {
        background-color: #e9ecef;
    }
    .sortable.asc .sort-icon,
    .sortable.desc .sort-icon {
        color: #0d6efd !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">Shift Management</h4>
        <button class="btn btn-primary" id="btnAddShift"><i class="bi bi-plus-lg"></i> Create Shift</button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Search Section -->
        <div class="row mb-3 g-2">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="searchInput" placeholder="Search by shift name...">
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="shiftsTable">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 sortable" data-sort="type">
                            Shift Name <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="start_time">
                            Start Time <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="end_time">
                            End Time <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="grace_time">
                            Grace Time <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody id="shiftsTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Loading shifts...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Section -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" id="paginationInfo">
                Showing 0-0 of 0
            </div>
            <nav aria-label="Shifts pagination">
                <ul class="pagination pagination-sm mb-0" id="paginationControls">
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add Shift Modal -->
<div class="modal fade" id="addShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Create New Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="addShiftForm">
                    <div class="mb-3">
                        <label for="shiftName" class="form-label small fw-semibold text-muted">SHIFT NAME</label>
                        <input type="text" class="form-control" id="shiftName" name="type" placeholder="e.g. Morning Shift">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="startTime" class="form-label small fw-semibold text-muted">START TIME</label>
                            <input type="time" class="form-control" id="startTime" name="start_time">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endTime" class="form-label small fw-semibold text-muted">END TIME</label>
                            <input type="time" class="form-control" id="endTime" name="end_time">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="graceTime" class="form-label small fw-semibold text-muted">GRACE TIME (MINUTES)</label>
                        <input type="number" class="form-control" id="graceTime" name="grace_time" placeholder="e.g. 15" min="0" max="120">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid pt-2">
                        <button id="add-submit" type="submit" class="btn btn-primary fw-semibold">Create Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Shift Modal -->
<div class="modal fade" id="editShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="editShiftForm">
                    <input type="hidden" id="editShiftId" name="id">
                    <div class="mb-3">
                        <label for="editShiftName" class="form-label small fw-semibold text-muted">SHIFT NAME</label>
                        <input type="text" class="form-control" id="editShiftName" name="type" placeholder="e.g. Morning Shift">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editStartTime" class="form-label small fw-semibold text-muted">START TIME</label>
                            <input type="time" class="form-control" id="editStartTime" name="start_time">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editEndTime" class="form-label small fw-semibold text-muted">END TIME</label>
                            <input type="time" class="form-control" id="editEndTime" name="end_time">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editGraceTime" class="form-label small fw-semibold text-muted">GRACE TIME (MINUTES)</label>
                        <input type="number" class="form-control" id="editGraceTime" name="grace_time" placeholder="e.g. 15" min="0" max="120">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid pt-2">
                        <button id="edit-submit" type="submit" class="btn btn-primary fw-semibold">Update Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Delete Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="deleteShiftId">
                <p class="text-muted mb-0">Are you sure you want to delete this shift ?</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-shift.js') ?>"></script>
<?= $this->endSection() ?>
