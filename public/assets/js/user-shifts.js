
document.addEventListener('DOMContentLoaded', function () {
    const assignShiftForm = document.getElementById('assignShiftForm');
    const bulkUserSearch = document.getElementById('bulkUserSearch');
    const bulkUsersTableBody = document.getElementById('bulkUsersTableBody');
    const selectAllUsers = document.getElementById('selectAllUsers');
    const btnBulkAssign = document.getElementById('btnBulkAssign');
    const bulkShiftId = document.getElementById('bulk_shift_id');
    const bulkSelectionCount = document.getElementById('bulkSelectionCount');

    let allUsers = [];
    let selectedUserIds = new Set();

    // --- Single Assignment Logic ---
    if (assignShiftForm) {
        assignShiftForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            clearFormErrors(this);

            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                const response = await fetch('/admin/assign-shift', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    updateCSRFToken(result.data?.csrf || result.errors?.csrf);
                    showAlert(result.message, 'success');
                    assignShiftForm.reset();
                    fetchBulkUsers(); // Refresh bulk list too
                } else {
                    updateCSRFToken(result.data?.csrf || result.errors?.csrf);
                    if (result.errors?.errors) {
                        const errors = result.errors.errors;
                        Object.entries(errors).forEach(([field, message]) => {
                            const input = assignShiftForm.querySelector(`[name="${field}"]`);
                            if (input) showError(input, message);
                        });
                    } else {
                        showAlert(result.message || 'An error occurred', 'error');
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('An unexpected error occurred', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // --- Bulk Assignment Logic ---

    // Fetch users for bulk assignment
    async function fetchBulkUsers() {
        try {
            bulkUsersTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading users...
                    </td>
                </tr>
            `;

            const response = await fetch('/admin/fetch-users?per_page=100&status=1', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const result = await response.json();

            if (result.status === 'success') {
                allUsers = result.data.users;
                renderBulkUsers(allUsers);
            } else {
                bulkUsersTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger">Failed to load users</td></tr>`;
            }
        } catch (error) {
            console.error('Error fetching users:', error);
            bulkUsersTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger">Error loading users</td></tr>`;
        }
    }

    function renderBulkUsers(users) {
        if (!users.length) {
            bulkUsersTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted">No users found</td></tr>`;
            return;
        }

        bulkUsersTableBody.innerHTML = users.map(user => `
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input user-checkbox" type="checkbox" value="${user.id}" 
                            ${selectedUserIds.has(user.id.toString()) ? 'checked' : ''}>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0D8ABC&color=fff" 
                             class="rounded-circle me-2" width="30" height="30">
                        <span class="fw-medium">${escapeHtml(user.name)}</span>
                    </div>
                </td>
                <td>${escapeHtml(user.email)}</td>
                <td><span class="badge ${user.shift_type ? 'bg-info' : 'bg-secondary'}">${user.shift_type || 'None'}</span></td>
            </tr>
        `).join('');

        // Re-attach listeners to checkboxes
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.checked) selectedUserIds.add(this.value);
                else selectedUserIds.delete(this.value);
                updateSelectionUI();
            });
        });
    }

    function updateSelectionUI() {
        bulkSelectionCount.textContent = `${selectedUserIds.size} users selected`;
        const checkboxes = document.querySelectorAll('.user-checkbox');
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        selectAllUsers.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
        selectAllUsers.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    }

    // Search functionality
    if (bulkUserSearch) {
        bulkUserSearch.addEventListener('input', debounce(function () {
            const query = this.value.toLowerCase();
            const filtered = allUsers.filter(user =>
                user.name.toLowerCase().includes(query) ||
                user.email.toLowerCase().includes(query)
            );
            renderBulkUsers(filtered);
        }, 300));
    }

    // Select All functionality
    if (selectAllUsers) {
        selectAllUsers.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                if (this.checked) selectedUserIds.add(cb.value);
                else selectedUserIds.delete(cb.value);
            });
            updateSelectionUI();
        });
    }

    // Bulk Assignment Submission
    if (btnBulkAssign) {
        btnBulkAssign.addEventListener('click', async function () {
            if (selectedUserIds.size === 0) {
                showAlert('Please select at least one user', 'error');
                return;
            }

            const shiftId = bulkShiftId.value;
            if (!shiftId) {
                showAlert('Please select a shift to assign', 'error');
                return;
            }

            const originalText = this.innerHTML;
            try {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Applying...';

                const response = await fetch('/admin/bulk-assign-shift', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        user_ids: Array.from(selectedUserIds),
                        shift_id: shiftId
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    updateCSRFToken(result.data?.csrf || result.errors?.csrf);
                    showAlert(result.message, 'success');
                    selectedUserIds.clear();
                    bulkShiftId.value = '';
                    fetchBulkUsers();
                    updateSelectionUI();
                } else {
                    updateCSRFToken(result.data?.csrf || result.errors?.csrf);
                    showAlert(result.message || 'An error occurred', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('An unexpected error occurred', 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    }

   

    function clearFormErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });
    }

    // Initialize Bulk Tab Users
    const bulkTabBtn = document.getElementById('bulk-tab');
    if (bulkTabBtn) {
        bulkTabBtn.addEventListener('shown.bs.tab', function () {
            if (allUsers.length === 0) fetchBulkUsers();
        });
    }
});
