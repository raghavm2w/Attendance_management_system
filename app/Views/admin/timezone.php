<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-0">Timezone Settings</h4>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold mb-0">System Timezone</h5>
                <p class="text-muted small mb-0 mt-2">Choose the timezone that will be used for all time-based records.</p>
            </div>
            <div class="card-body">
                <form id="timezoneForm">
                    <div class="mb-4">
                        <label for="systemTimezone" class="form-label small fw-semibold text-muted">SELECT TIMEZONE</label>
                        <select class="form-select" id="systemTimezone" name="timezone">
                            <option value="UTC" <?= $currentTimezone == 'UTC' ? 'selected' : '' ?>>UTC (GMT+0)</option>
                            <option value="Asia/Kolkata" <?= $currentTimezone == 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (GMT+5:30)</option>
                            <option value="America/New_York" <?= $currentTimezone == 'America/New_York' ? 'selected' : '' ?>>America/New_York (GMT-5)</option>
                            <option value="Europe/London" <?= $currentTimezone == 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT+0)</option>
                            <option value="Asia/Dubai" <?= $currentTimezone == 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (GMT+4)</option>
                            <option value="Australia/Sydney" <?= $currentTimezone == 'Australia/Sydney' ? 'selected' : '' ?>>Australia/Sydney (GMT+11)</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-settings.js') ?>"></script>
<?= $this->endSection() ?>
