<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">User Management</h4>
        <button class="btn btn-primary" id="btnAddUser"><i class="bi bi-plus-lg"></i> Add New User</button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
         <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">User</th>
                        <th class="border-0">Role</th>
                        <th class="border-0">Department</th>
                         <th class="border-0">Shift</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-info-circle me-2"></i> No users found. 
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-user.js') ?>"></script>
<?= $this->endSection() ?>

