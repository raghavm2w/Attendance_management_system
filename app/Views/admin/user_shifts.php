<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Assign Shift to User</h1>
            <p class="text-muted">Manage shift assignments for users</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-gear me-2"></i>Assign Shift</h5>
                </div>
                <div class="card-body p-4">
                    
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
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 bg-light">
                <div class="card-body p-4">
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
                         <li class="list-group-item bg-transparent px-0 border-0 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>If the user already has a shift assigned, it will be updated to the new one.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/user-shifts.js') ?>"></script>
<?= $this->endSection() ?>
