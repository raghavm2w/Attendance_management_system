<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold">Leave Requests</h4>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom-0">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">History</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
         <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">User</th>
                        <th class="border-0">Type</th>
                        <th class="border-0">Dates</th>
                        <th class="border-0">Reason</th>
                         <th class="border-0">Status</th>
                        <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                     <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i> No pending requests. (UI Mockup)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
