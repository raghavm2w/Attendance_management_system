document.addEventListener('DOMContentLoaded', function () {
    const leaveTypesTableBody = document.getElementById('leaveTypesTableBody');
    const addLeaveTypeForm = document.getElementById('addLeaveTypeForm');
    const editLeaveTypeForm = document.getElementById('editLeaveTypeForm');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Fetch and render leave types
    async function fetchLeaveTypes() {
        try {
            const response = await fetch('/admin/policy/leave', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const res = await response.json();

            if (res.status === "success") {
                updateCSRFToken(res.data.csrf);
                renderTable(res.data.data);
            } else {
                showAlert(res.message || 'Failed to fetch leave types', 'error');
            }
        } catch (err) {
            console.error('Error:', err);
            showAlert('Something went wrong. Please try again.', 'error');
        }
    }

    function renderTable(data) {
        if (!data || data.length === 0) {
            leaveTypesTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No leave types found.</td></tr>`;
            return;
        }

        leaveTypesTableBody.innerHTML = data.map(item => `
            <tr>
                <td class="fw-semibold">${escapeHtml(item.name)}</td>
                <td>${escapeHtml(item.max_per_year)}</td>
                <td>
                    <span class="badge ${item.carry_forward == 1 ? 'bg-success' : 'bg-secondary'}">
                        ${item.carry_forward == 1 ? 'Yes' : 'No'}
                    </span>
                </td>
                <td>${item.carry_forward == 1 ? escapeHtml(item.max_carry) : '0'}</td>
                <td>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-item='${JSON.stringify(item)}'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${item.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        // Attach event listeners
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const item = JSON.parse(this.dataset.item);
                openEditModal(item);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                document.getElementById('deleteLeaveTypeId').value = id;
                const modal = new bootstrap.Modal(document.getElementById('deleteLeaveTypeModal'));
                modal.show();
            });
        });
    }

    // Helper to clear all errors in a form
    function clearAllLeaveErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(el => clearError(el));
    }

    // Clear errors when opening add modal
    document.getElementById('addLeaveTypeModal').addEventListener('show.bs.modal', function () {
        addLeaveTypeForm.reset();
        clearAllLeaveErrors(addLeaveTypeForm);
        addLeaveTypeForm.querySelector('.maxCarryDiv').style.display = 'none';
    });

    // Clear errors when opening edit modal
    document.getElementById('editLeaveTypeModal').addEventListener('show.bs.modal', function () {
        clearAllLeaveErrors(editLeaveTypeForm);
    });

    // Toggle Max Carry Limit field
    document.querySelectorAll('.carryForwardSwitch').forEach(sw => {
        sw.addEventListener('change', function () {
            const container = this.closest('form').querySelector('.maxCarryDiv');
            container.style.display = this.checked ? 'block' : 'none';
        });
    });

    // Add Leave Type
    addLeaveTypeForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = document.getElementById('btnAddSubmit');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        try {
            const response = await fetch('/admin/policy/leave', {
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
                bootstrap.Modal.getInstance(document.getElementById('addLeaveTypeModal')).hide();
                updateCSRFToken(res.data.csrf);
                fetchLeaveTypes();
            } else {
                updateCSRFToken(res.errors?.csrf);
                handleValidationErrors(res.errors?.errors || {}, addLeaveTypeForm);
            }
        } catch (err) {
            console.error('Error:', err);
            showAlert('Something went wrong. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Open Edit Modal
    function openEditModal(item) {
        document.getElementById('editLeaveTypeId').value = item.id;
        document.getElementById('editLeaveName').value = item.name;
        document.getElementById('editMaxPerYear').value = item.max_per_year;

        const cfSwitch = document.getElementById('editCarryForwardSwitch');
        cfSwitch.checked = item.carry_forward == 1;

        const mcDiv = document.querySelector('#editLeaveTypeForm .maxCarryDiv');
        mcDiv.style.display = item.carry_forward == 1 ? 'block' : 'none';
        document.getElementById('editMaxCarry').value = item.max_carry;

        new bootstrap.Modal(document.getElementById('editLeaveTypeModal')).show();
    }

    // Update Leave Type
    editLeaveTypeForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('editLeaveTypeId').value;
        const formData = new FormData(this);
        const btn = document.getElementById('btnEditSubmit');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        try {
            const response = await fetch(`/admin/policy/leave/update/${id}`, {
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
                bootstrap.Modal.getInstance(document.getElementById('editLeaveTypeModal')).hide();
                updateCSRFToken(res.data?.csrf);
                fetchLeaveTypes();
                return;
            } else {
                updateCSRFToken(res.errors?.csrf);
                handleValidationErrors(res.errors?.errors || {}, editLeaveTypeForm);
            }
        } catch (err) {
            console.error('Error:', err);
            showAlert('Something went wrong. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Delete Leave Type
    confirmDeleteBtn.addEventListener('click', async function () {
        const id = document.getElementById('deleteLeaveTypeId').value;
        const originalText = this.innerHTML;

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

        try {
            const response = await fetch(`/admin/policy/leave/delete/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const res = await response.json();

            if (res.status === "success") {
                showAlert(res.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteLeaveTypeModal')).hide();
                updateCSRFToken(res.data.csrf);
                fetchLeaveTypes();
            } else {
                updateCSRFToken(res.errors?.csrf);
                showAlert(res.message || 'Failed to delete leave type', 'error');
            }
        } catch (err) {
            console.error('Error:', err);
            showAlert('Something went wrong. Please try again.', 'error');
        } finally {
            this.disabled = false;
            this.innerHTML = originalText;
        }
    });

    function handleValidationErrors(errors, form) {
        clearAllLeaveErrors(form);
        for (const [field, message] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                showError(input, message);
            }
        }
    }

    // Initial fetch
    fetchLeaveTypes();
});
