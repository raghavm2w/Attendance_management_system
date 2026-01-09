<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">Shift Management</h4>
        <button class="btn btn-primary"><i class="bi bi-plus-lg"></i> Create Shift</button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
         <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                         <th class="border-0">Shift Name</th>
                        <th class="border-0">Start Time</th>
                        <th class="border-0">End Time</th>
                        <th class="border-0">Grace Time</th>
                        <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                     <tr>
                        <td>Morning Shift</td>
                        <td>09:00 AM</td>
                        <td>06:00 PM</td>
                        <td>15 Mins</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
