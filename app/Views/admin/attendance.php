<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">Attendance Logs</h4>
        <div>
             <button class="btn btn-outline-secondary me-2"><i class="bi bi-download"></i> Export</button>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
         <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">Date</th>
                        <th class="border-0">User</th>
                         <th class="border-0">Check In</th>
                        <th class="border-0">Check Out</th>
                        <th class="border-0">Status</th>
                          <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x me-2"></i> No records found for today. (UI Mockup)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
