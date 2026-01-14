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
    .sortable.asc .sort-icon::before {
        content: "\F128";
    }
    .sortable.desc .sort-icon::before {
        content: "\F127";
    }
    .sortable.asc .sort-icon,
    .sortable.desc .sort-icon {
        color: #0d6efd !important;
    }
    .badge-role-admin {
        background-color: #dc3545;
    }
    .badge-role-user {
        background-color: #0d6efd;
    }
    .badge-shift-day {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-shift-night {
        background-color: #6f42c1;
    }
    .edit{
        border:none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">User Management</h4>
        <button class="btn btn-primary" id="btnAddUser"><i class="bi bi-plus-lg"></i> Add New User</button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Search and Filter Section -->
        <div class="row mb-3 g-2">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="searchInput" placeholder="Search by name or email...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">Employee</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="shiftFilter">
                    <option value="">All Shifts</option>
                    <option value="day">Day</option>
                    <option value="night">Night</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="1" selected>Active</option>
                    <option value="0">Deleted</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-grey w-50" id="btnExport"><i class="bi bi-download"></i> Export</button>
                <button class="btn btn-outline-grey w-50" id="btnImport"><i class="bi bi-upload"></i> Import</button>
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="usersTable">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 sortable" data-sort="name">
                            User <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="email">
                            Email <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="role">
                            Role <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="shift">
                            Shift <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="is_active">
                            Status <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="created_at">
                            Created-at <i class="bi bi-arrow-down-up ms-1 text-muted sort-icon"></i>
                        </th>
                        <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Loading users...
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
            <nav aria-label="Users pagination">
                <ul class="pagination pagination-sm mb-0" id="paginationControls">
                    <!-- Pagination buttons will be inserted here -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label for="userName" class="form-label small fw-semibold text-muted">FULL NAME</label>
                        <input type="text" class="form-control" id="userName" name="name" placeholder="e.g. John Doe">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label small fw-semibold text-muted">EMAIL ADDRESS</label>
                        <input type="email" class="form-control" id="userEmail" name="email" placeholder="john@example.com">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label small fw-semibold text-muted">PASSWORD</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="userPassword" name="password" placeholder="••••••••">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                         <label for="userRole" class="form-label small fw-semibold text-muted">ROLE</label>
                        <select class="form-select" id="userRole" name="role">
                            <option value="user" selected>Employee</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid pt-2">
                        <button id="add-submit" type="submit" class="btn btn-primary fw-semibold">Create User</button>
                    </div>
                    <div class="loader hidden">
                        <div class="spinner"></div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Import Users Modal -->
<div class="modal fade" id="importUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="importUserForm" enctype="multipart/form-data">
                    <div class="mb-4 text-center">
                         <div class="mb-3">
                            <i class="bi bi-file-earmark-excel text-success display-4"></i>
                         </div>
                         <p class="text-muted small">Upload a CSV or Excel file to import users in bulk.</p>
                         <a href="<?= base_url('assets/samples/users_import_sample.csv') ?>" class="text-decoration-none small" download><i class="bi bi-download me-1"></i>Download Sample Template</a>
                    </div>
                    
                    <div class="mb-3">
                        <label for="importFile" class="form-label small fw-semibold text-muted">SELECT FILE</label>
                        <input type="file" class="form-control" id="importFile" name="file" accept=".csv, .xlsx, .xls" required>
                        <div class="invalid-feedback">Please select a valid file.</div>
                    </div>
                    
                    <div class="d-grid pt-2">
                        <button id="import-submit" type="submit" class="btn btn-primary fw-semibold">Import Users</button>
                    </div>
                    <div class="loader hidden">
                        <div class="spinner"></div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label small fw-semibold text-muted">FULL NAME</label>
                        <input type="text" class="form-control" id="editUserName" name="name" placeholder="e.g. John Doe">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label small fw-semibold text-muted">EMAIL ADDRESS</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" placeholder="john@example.com">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                         <label for="editUserRole" class="form-label small fw-semibold text-muted">ROLE</label>
                        <select class="form-select" id="editUserRole" name="role">
                            <option value="user">Employee</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid pt-2">
                        <button id="edit-submit" type="submit" class="btn btn-primary fw-semibold">Update User</button>
                    </div>
                    <div class="loader hidden">
                        <div class="spinner"></div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="deleteUserId">
                <p class="text-muted mb-0">Are you sure you want to delete this user ? This action will deactivate the user.</p>
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
<script src="<?= base_url('assets/js/admin-user.js') ?>"></script>
<?= $this->endSection() ?>

