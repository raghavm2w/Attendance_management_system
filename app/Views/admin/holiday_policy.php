<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<style>
    .day-selector {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .day-checkbox {
        display: none;
    }
    .day-label {
        padding: 8px 15px;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.9rem;
    }
    .day-checkbox:checked + .day-label {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold">Holiday & Weekly Off Policy</h4>
    </div>
</div>

<div class="row g-4">
    <!-- Weekly Off Section -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold mb-0">Weekly Off Days</h5>
                <p class="text-muted small mb-0">Select days that are considered as standard weekly offs.</p>
            </div>
            <div class="card-body">
                <form id="weeklyOffForm">
                    <div class="day-selector mb-4">
                        <?php 
                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach($days as $index => $day): 
                        ?>
                        <input type="checkbox" id="day_<?= $index ?>" class="day-checkbox" name="days[]" value="<?= $index ?>" <?= $index == 0 ? 'checked' : '' ?>>
                        <label for="day_<?= $index ?>" class="day-label"><?= $day ?></label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Update Weekly Offs</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Holidays Section -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">Public Holidays</h5>
                    <p class="text-muted small mb-0">Manage specific dates for holidays.</p>
                </div>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                    <i class="bi bi-plus-lg"></i> Add Holiday
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Date</th>
                                <th class="border-0">Holiday Name</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="holidaysTableBody">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading holidays...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Holiday Modal -->
<div class="modal fade" id="holidayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="holidayModalTitle">Add Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="holidayForm">
                    <input type="hidden" name="id" id="holidayId">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">HOLIDAY DATE</label>
                        <input type="date" class="form-control" min=<?= date('Y-m-d') ?> name="holiday_date" id="holidayDate">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">HOLIDAY NAME</label>
                        <input type="text" class="form-control" name="name" id="holidayName" placeholder="e.g. Christmas">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">HOLIDAY TYPE</label>
                        <select class="form-select" name="is_optional" id="holidayIsOptional">
                            <option value="0">Mandatory</option>
                            <option value="1">Optional</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid pt-2">
                        <button type="submit" class="btn btn-primary fw-semibold" id="btnSaveHoliday">Save Holiday</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteHolidayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Delete Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="deleteHolidayId">
                <p class="text-muted mb-0">Are you sure you want to delete this holiday?</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteHolidayBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/policy-holiday.js') ?>"></script>
<?= $this->endSection() ?>
