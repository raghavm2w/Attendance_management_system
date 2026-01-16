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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">Leave Policy Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaveTypeModal">
            <i class="bi bi-plus-lg"></i> Add Leave Type
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">Leave Name</th>
                        <th class="border-0">Max Per Year</th>
                        <th class="border-0">Carry Forward</th>
                        <th class="border-0">Max Carry</th>
                        <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody id="leaveTypesTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Loading leave types...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Leave Type Modal -->
<div class="modal fade" id="addLeaveTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="addLeaveTypeForm">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">LEAVE NAME</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Sick Leave">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">MAX PER YEAR</label>
                        <input type="number" class="form-control" name="max_per_year" placeholder="e.g. 12" step="0.5" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input carryForwardSwitch" type="checkbox" id="addCarryForwardSwitch" name="carry_forward" value="1">
                            <label class="form-check-label small fw-semibold text-muted" for="addCarryForwardSwitch">CARRY FORWARD</label>
                        </div>
                    </div>
                    <div class="mb-3 maxCarryDiv" style="display: none;">
                        <label class="form-label small fw-semibold text-muted">MAX CARRY LIMIT</label>
                        <input type="number" class="form-control" name="max_carry" placeholder="e.g. 5" step="0.5" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid pt-2">
                        <button type="submit" class="btn btn-primary fw-semibold" id="btnAddSubmit">Save Leave Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Leave Type Modal -->
<div class="modal fade" id="editLeaveTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="editLeaveTypeForm">
                    <input type="hidden" id="editLeaveTypeId" name="id">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">LEAVE NAME</label>
                        <input type="text" class="form-control" name="name" id="editLeaveName">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">MAX PER YEAR</label>
                        <input type="number" class="form-control" name="max_per_year" id="editMaxPerYear" step="0.5" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input carryForwardSwitch" type="checkbox" id="editCarryForwardSwitch" name="carry_forward" value="1">
                            <label class="form-check-label small fw-semibold text-muted" for="editCarryForwardSwitch">CARRY FORWARD</label>
                        </div>
                    </div>
                    <div class="mb-3 maxCarryDiv" style="display: none;">
                        <label class="form-label small fw-semibold text-muted">MAX CARRY LIMIT</label>
                        <input type="number" class="form-control" name="max_carry" id="editMaxCarry" step="0.5" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid pt-2">
                        <button type="submit" class="btn btn-primary fw-semibold" id="btnEditSubmit">Update Leave Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteLeaveTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Delete Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="deleteLeaveTypeId">
                <p class="text-muted mb-0">Are you sure you want to delete this leave type?</p>
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
<script src="<?= base_url('assets/js/policy-leave.js') ?>"></script>
<?= $this->endSection() ?>
