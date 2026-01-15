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
    .edit {
        border: none;
        background: transparent;
        color: #0d6efd;
    }
    .delete {
        border: none;
        background: transparent;
        color: #dc3545;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-0">IP Address Settings</h4>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Allowed IP Addresses</h5>
            <button class="btn btn-primary btn-sm" id="btnAddIp"><i class="bi bi-plus-lg"></i> Add IP Address</button>
        </div>
        <p class="text-muted small mb-0 mt-2">Manage the list of IP addresses allowed to access the system.</p>
    </div>
    <div class="card-body">
        <!-- Search and Filter Section -->
        <div class="row mb-3 g-2">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="ipSearchInput" placeholder="Search by label...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="ipStatusFilter">
                    <option value="1" selected>Active</option>
                    <option value="0">Deleted</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="ipsTable">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">Label</th>
                        <th class="border-0">IP Address</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Created At</th>
                        <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody id="ipsTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Loading IP addresses...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" id="ipPaginationInfo">
                Showing 0-0 of 0
            </div>
            <nav aria-label="IPs pagination">
                <ul class="pagination pagination-sm mb-0" id="ipPaginationControls">
                    <!-- Pagination buttons will be inserted here -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add IP Modal -->
<div class="modal fade" id="addIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Add IP Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="addIpForm">
                    <div class="mb-3">
                        <label for="ipLabel" class="form-label small fw-semibold text-muted">LABEL</label>
                        <input type="text" class="form-control" id="ipLabel" name="label" placeholder="e.g. Office Wi-Fi" required>
                        <div class="invalid-feedback"></div>

                    </div>
                    <div class="mb-3">
                        <label for="ipAddress" class="form-label small fw-semibold text-muted">IP ADDRESS</label>
                        <input type="text" class="form-control" id="ipAddress" name="ip_address" placeholder="e.g. 192.168.1.1" required>
                        <div class="invalid-feedback"></div>

                    </div>
                    <div class="d-grid pt-2">
                        <button type="submit" class="btn btn-primary fw-semibold">Add IP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit IP Modal -->
<div class="modal fade" id="editIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit IP Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="editIpForm">
                    <input type="hidden" id="editIpId" name="id">
                    <div class="mb-3">
                        <label for="editIpLabel" class="form-label small fw-semibold text-muted">LABEL</label>
                        <input type="text" class="form-control" id="editIpLabel" name="label" required>
                    </div>
                    <div class="mb-3">
                        <label for="editIpAddress" class="form-label small fw-semibold text-muted">IP ADDRESS</label>
                        <input type="text" class="form-control" id="editIpAddress" name="ip_address" required>
                    </div>
                    <div class="d-grid pt-2">
                        <button type="submit" class="btn btn-primary fw-semibold">Update IP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Delete IP Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="deleteIpId">
                <p class="text-muted mb-0">Are you sure you want to delete this IP address? This will deactivate it.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteIpBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-settings.js') ?>"></script>
<?= $this->endSection() ?>
