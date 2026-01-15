<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Assign Shift to User</h1>
            <p class="text-muted">Manage shift assignments for users individually or in bulk</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <ul class="nav nav-tabs border-bottom-0" id="shiftTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold px-4 border-0" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab" aria-controls="single" aria-selected="true">
                                <i class="bi bi-person me-2"></i>Single Assignment
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold px-4 border-0" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab" aria-controls="bulk" aria-selected="false">
                                <i class="bi bi-people me-2"></i>Bulk Assignment
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="shiftTabsContent">
                        <!-- Single Assignment Tab -->
                        <div class="tab-pane fade show active p-4" id="single" role="tabpanel" aria-labelledby="single-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-4 fw-bold text-primary"><i class="bi bi-person-gear me-2"></i>Assign Shift</h5>
                                    <form id="assignShiftForm">
                                        <div class="mb-4">
                                            <label for="email" class="form-label fw-medium text-secondary">User Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                                <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" 
                                                    placeholder="Enter user email address" required value="<?= old('email') ?>">
                                            </div>
                                            <div class="form-text">Enter the email of the registered active user.</div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="shift_id" class="form-label fw-medium text-secondary">Select Shift</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-clock text-muted"></i></span>
                                                <select class="form-select border-start-0 ps-0" id="shift_id" name="shift_id" required>
                                                    <option value="" selected disabled>Choose a shift...</option>
                                                    <?php if (!empty($shifts)): ?>
                                                        <?php foreach ($shifts as $shift): ?>
                                                            <option value="<?= $shift['id'] ?>" <?= old('shift_id') == $shift['id'] ? 'selected' : '' ?>>
                                                                <?= esc($shift['type']) ?> (<?= substr($shift['start_time'], 0, 5) ?> - <?= substr($shift['end_time'], 0, 5) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary py-2 text-uppercase fw-bold text-white shadow-sm" style="letter-spacing: 0.5px;">
                                                <i class="bi bi-save me-2"></i>Save Assignment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 border-start d-none d-md-block">
                                    <div class="ps-4">
                                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2"></i>Instructions</h5>
                                        <ul class="list-group list-group-flush bg-transparent">
                                            <li class="list-group-item bg-transparent px-0 border-0 d-flex align-items-start">
                                                <i class="bi bi-1-circle-fill text-primary me-2 mt-1"></i>
                                                <div>Enter the email address of the user you wish to assign a shift to. The user must exist and be active.</div>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 border-0 d-flex align-items-start">
                                                <i class="bi bi-2-circle-fill text-primary me-2 mt-1"></i>
                                                <div>Select the desired shift from the dropdown list.</div>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 border-0 d-flex align-items-start">
                                                <i class="bi bi-3-circle-fill text-primary me-2 mt-1"></i>
                                                <div>Click "Save Assignment" to update the user's shift.</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Assignment Tab -->
                        <div class="tab-pane fade p-4" id="bulk" role="tabpanel" aria-labelledby="bulk-tab">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">1. Search & Select Users</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="bulkUserSearch" placeholder="Search by name or email...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">2. Select Shift to Assign</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-clock text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" id="bulk_shift_id" required>
                                            <option value="" selected disabled>Choose a shift...</option>
                                            <?php if (!empty($shifts)): ?>
                                                <?php foreach ($shifts as $shift): ?>
                                                    <option value="<?= $shift['id'] ?>">
                                                        <?= esc($shift['type']) ?> (<?= substr($shift['start_time'], 0, 5) ?> - <?= substr($shift['end_time'], 0, 5) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <button class="btn btn-primary fw-bold" id="btnBulkAssign">
                                            <i class="bi bi-save me-2"></i>Apply Bulk
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover align-middle" id="bulkUsersTable">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th class="border-0" style="width: 40px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                                </div>
                                            </th>
                                            <th class="border-0">User</th>
                                            <th class="border-0">Email</th>
                                            <th class="border-0">Current Shift</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulkUsersTableBody">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                                Loading users...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 text-muted small" id="bulkSelectionCount">
                                0 users selected
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/user-shifts.js') ?>"></script>
<?= $this->endSection() ?>
