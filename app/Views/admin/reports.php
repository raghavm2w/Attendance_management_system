<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold">Reports & Analytics</h4>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
         <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Attendance Summary</h5>
                 <div class="d-flex justify-content-center align-items-center" style="height: 200px; background: #f8f9fa; border-radius: 8px;">
                    <span class="text-muted">Chart Placeholder</span>
                </div>
            </div>
         </div>
    </div>
     <div class="col-md-6">
         <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Generate Report</h5>
                <form>
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <input type="date" class="form-control mb-2">
                        <input type="date" class="form-control">
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Report Type</label>
                        <select class="form-select">
                            <option>Daily Attendance</option>
                            <option>Monthly Summary</option>
                            <option>Late Comer Report</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Download Report</button>
                </form>
            </div>
         </div>
    </div>
</div>
<?= $this->endSection() ?>
