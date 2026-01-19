document.addEventListener('DOMContentLoaded', function () {
    const weeklyOffForm = document.getElementById('weeklyOffForm');
    const holidaysTableBody = document.getElementById('holidaysTableBody');
    const holidayForm = document.getElementById('holidayForm');
    const confirmDeleteHolidayBtn = document.getElementById('confirmDeleteHolidayBtn');

    // Fetch and render data
    async function fetchPolicyData() {
        try {
            const response = await fetch('/admin/policy/holiday', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const res = await response.json();

            if (res.status === "success") {
                updateCSRFToken(res.data.csrf);
                renderWeeklyOffs(res.data.weekly_offs);
                renderHolidays(res.data.holidays);
            }

        } catch (err) {
            console.error('Error:', err);
            showAlert("Error fetching data", "error");
        }
    }

    function renderWeeklyOffs(data) {
        if (!data) return;
        document.querySelectorAll('.day-checkbox').forEach(cb => {
            cb.checked = data.some(off => off.day_of_week == cb.value);
        });
    }

    function renderHolidays(data) {
        if (!data || data.length === 0) {
            holidaysTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted">No holidays found.</td></tr>`;
            return;
        }

        holidaysTableBody.innerHTML = data.map(item => `
            <tr>
                <td>${escapeHtml(item.holiday_date)}</td>
                <td class="fw-semibold">${escapeHtml(item.name)}</td>
                <td>
                    <span class="badge ${item.is_optional == 1 ? 'bg-secondary' : 'bg-primary'}">
                        ${item.is_optional == 1 ? 'Optional' : 'Mandatory'}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary edit-holiday-btn" data-item='${JSON.stringify(item)}'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-holiday-btn" data-id="${item.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        // Attach event listeners
        document.querySelectorAll('.edit-holiday-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const item = JSON.parse(this.dataset.item);
                openHolidayModal(item);
            });
        });

        document.querySelectorAll('.delete-holiday-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                document.getElementById('deleteHolidayId').value = id;
                new bootstrap.Modal(document.getElementById('deleteHolidayModal')).show();
            });
        });
    }

    // Helper to clear all errors in holiday form
    function clearAllHolidayErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(el => clearError(el));
    }

    // Clear errors when opening modal
    document.getElementById('holidayModal').addEventListener('show.bs.modal', function () {
        if (!document.getElementById('holidayId').value) {
            holidayForm.reset();
            document.getElementById('holidayModalTitle').textContent = 'Add Holiday';
        }
        clearAllHolidayErrors(holidayForm);
    });

    // Update Weekly Off
    weeklyOffForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        try {
            const response = await fetch('/admin/policy/holiday/update-weekly-off', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const res = await response.json();

            if (res.status === "success") {
                showAlert(res.message, 'success');
                updateCSRFToken(res.data.csrf);
            } else {
                updateCSRFToken(res.errors?.csrf);
                showAlert(res.message, 'error');
            }
        } catch (err) {
            console.error('Error:', err);
            showAlert('Something went wrong. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Save Holiday (Add/Edit)
    holidayForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = document.getElementById('btnSaveHoliday');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        try {
            const response = await fetch('/admin/policy/holiday/save', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const res = await response.json();

            if (res.status === "success") {
                showAlert(res.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('holidayModal')).hide();
                updateCSRFToken(res.data.csrf);
                fetchPolicyData();
            } else {
                updateCSRFToken(res.errors?.csrf);
                handleValidationErrors(res.errors?.errors || {}, holidayForm);
            }
        } catch (err) {
            console.error('Error:', err);
            showAlert('Something went wrong. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Delete Holiday
    confirmDeleteHolidayBtn.addEventListener('click', async function () {
        const id = document.getElementById('deleteHolidayId').value;
        const originalText = this.innerHTML;

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

        try {
            const response = await fetch(`/admin/policy/holiday/delete/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const res = await response.json();

            if (res.status === "success") {
                showAlert(res.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteHolidayModal')).hide();
                updateCSRFToken(res.data.csrf);
                fetchPolicyData();
            } else {
                updateCSRFToken(res.errors?.csrf);
                showAlert(res.message, 'error');
            }
        } catch (err) {
            console.error('Error:', err);
            showAlert('Something went wrong. Please try again.', 'error');
        } finally {
            this.disabled = false;
            this.innerHTML = originalText;
        }
    });

    function openHolidayModal(item = null) {
        if (item) {
            document.getElementById('holidayId').value = item.id;
            document.getElementById('holidayDate').value = item.holiday_date;
            document.getElementById('holidayName').value = item.name;
            document.getElementById('holidayIsOptional').value = item.is_optional;
            document.getElementById('holidayModalTitle').textContent = 'Edit Holiday';
        } else {
            document.getElementById('holidayId').value = '';
            holidayForm.reset();
            document.getElementById('holidayModalTitle').textContent = 'Add Holiday';
        }
        new bootstrap.Modal(document.getElementById('holidayModal')).show();
    }

    // Bind Add Holiday button 
    const addHolidayBtn = document.querySelector('[data-bs-target="#addHolidayModal"]');
    if (addHolidayBtn) {
        addHolidayBtn.removeAttribute('data-bs-toggle');
        addHolidayBtn.removeAttribute('data-bs-target');
        addHolidayBtn.addEventListener('click', () => openHolidayModal());
    }

    function handleValidationErrors(errors, form) {
        clearAllHolidayErrors(form);
        for (const [field, message] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                showError(input, message);
            }
        }
    }

    // Initial fetch
    fetchPolicyData();
});
