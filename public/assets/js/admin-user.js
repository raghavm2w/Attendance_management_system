document.addEventListener('DOMContentLoaded', function () {
    const addUserBtn = document.getElementById('btnAddUser');
    const addUserModalElement = document.getElementById('addUserModal');
    const addUserForm = document.getElementById('addUserForm');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('userPassword');
    const addsubmitBtn = document.getElementById("add-submit");

    // Initialize Bootstrap Modal
    const addUserModal = new bootstrap.Modal(addUserModalElement);

    // Event Listener for Opening Modal
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function () {
            addUserForm.reset();
            clearAllErrors();
            addUserModal.show();
        });
    }

    // Password Toggle Logic
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle Icon
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            }
        });
    }

    // Input Identifiers
    const nameInput = document.getElementById('userName');
    const emailInput = document.getElementById('userEmail');
    const roleInput = document.getElementById('userRole');

    function validateName() {
        const value = nameInput.value.trim();
        if (!value) {
            showError(nameInput, 'Full name is required');
            return false;
        } else if (value.length < 3) {
            showError(nameInput, 'Name must be at least 3 characters');
            return false;
        }
        clearError(nameInput);
        return true;
    }

    function validateEmail() {
        const value = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!value) {
            showError(emailInput, 'Email address is required');
            return false;
        } else if (!emailRegex.test(value)) {
            showError(emailInput, 'Please enter a valid email address');
            return false;
        }
        clearError(emailInput);
        return true;
    }

    function validatePassword() {
        const value = passwordInput.value;
        if (!value) {
            showError(passwordInput, 'Password is required');
            return false;
        } else if (value.length < 6) {
            showError(passwordInput, 'Password must be at least 6 characters');
            return false;
        }
        clearError(passwordInput);
        return true;
    }

    function validateRole() {
        if (!roleInput.value) {
            showError(roleInput, 'Please select a role');
            return false;
        }
        clearError(roleInput);
        return true;
    }

    if (nameInput) nameInput.addEventListener('input', validateName);
    if (emailInput) emailInput.addEventListener('input', validateEmail);
    if (passwordInput) passwordInput.addEventListener('input', validatePassword);
    if (roleInput) roleInput.addEventListener('change', validateRole);


    if (addUserForm) {
        addUserForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const isNameValid = validateName();
            const isEmailValid = validateEmail();
            const isPasswordValid = validatePassword();
            const isRoleValid = validateRole();

            if (!isNameValid || !isEmailValid || !isPasswordValid || !isRoleValid) {
                return;
            }

            const formData = new FormData(addUserForm);
            const data = Object.fromEntries(formData.entries());

            try {

                showLoader(true);
                const response = await fetch('/admin/add-user', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.status === "error") {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.errors.csrf);

                    if (result.errors.errors) {
                        Object.entries(result.errors.errors).forEach(([field, message]) => {
                            const input = addUserForm[field];
                            if (input) {
                                showError(input, message);
                            }
                        });
                    } else {
                        showAlert(result.message, "error");
                        addUserForm.reset();
                    }
                    return;
                }
                if (result.data.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.data.csrf);
                }
                showAlert(result.message, "success");
                addUserModal.hide();
                addUserForm.reset();
                fetchUsers();



            } catch (error) {
                console.error('Error:', error);
            } finally {
                showLoader(false);

            }
        });
    }
    function showLoader(show) {
        document.querySelector('.loader').classList.toggle('hidden', !show);
        addsubmitBtn.disabled = show;

    }
   

    function showError(input, message) {
        input.classList.add('is-invalid');
        // Handle input-group hierarchy for password field
        const parent = input.closest('.input-group') || input.parentElement;
        const feedback = parent.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
            // Ensure feedback is visible even if inside input-group
            feedback.style.display = 'block';
        }
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        const parent = input.closest('.input-group') || input.parentElement;
        const feedback = parent.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
            feedback.style.display = 'none';
        }
    }

    function clearAllErrors() {
        const inputs = addUserForm.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => clearError(input));
    }

    // --------------- FETCH USERS FUNCTIONALITY 

    let currentState = {
        search: '',
        role: '',
        shift: '',
        status: '',
        sortBy: 'created_at',
        sortOrder: 'desc',
        page: 1,
        perPage: 5
    };

    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const shiftFilter = document.getElementById('shiftFilter');
    const statusFilter = document.getElementById('statusFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const usersTableBody = document.getElementById('usersTableBody');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const sortableHeaders = document.querySelectorAll('.sortable');

    // Fetch users from API
    async function fetchUsers() {
        try {
            const params = new URLSearchParams({
                search: currentState.search,
                role: currentState.role,
                shift: currentState.shift,
                status: currentState.status,
                sort_by: currentState.sortBy,
                sort_order: currentState.sortOrder,
                page: currentState.page,
                per_page: currentState.perPage
            });

            // Show loading state
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading users...
                    </td>
                </tr>
            `;

            const response = await fetch(`/admin/fetch-users?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                renderUsersTable(result.data.users);
                updatePaginationInfo(result.data.pagination);
                renderPaginationControls(result.data.pagination);
            } else {
                showAlert(result.message || 'Failed to fetch users', 'error');
                usersTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-exclamation-circle me-2"></i> Failed to load users
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Fetch error:', error);
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-exclamation-circle me-2"></i> Error loading users
                    </td>
                </tr>
            `;
        }
    }

    // Render users table
    function renderUsersTable(users) {
        if (!users || users.length === 0) {
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle me-2"></i> No records found
                    </td>
                </tr>
            `;
            return;
        }

        usersTableBody.innerHTML = users.map(user => {
            const roleBadge = user.role === 'admin'
                ? '<span class="badge badge-role-admin">Admin</span>'
                : '<span class="badge badge-role-user">Employee</span>';

            const shiftBadge = user.shift_type
                ? (user.shift_type.toLowerCase().includes('night') || user.shift_type.toLowerCase() === 'night'
                    ? `<span class="badge badge-shift-night">${escapeHtml(user.shift_type)}</span>`
                    : `<span class="badge badge-shift-day">${escapeHtml(user.shift_type)}</span>`)
                : '<span class="badge bg-secondary">Not Assigned</span>';

            const statusBadge = user.is_active == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            const createdAt = new Date(user.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            return `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0D8ABC&color=fff" 
                                 class="rounded-circle me-2" width="35" height="35" alt="${escapeHtml(user.name)}">
                            <span class="fw-medium">${escapeHtml(user.name)}</span>
                        </div>
                    </td>
                    <td>${escapeHtml(user.email)}</td>
                    <td>${roleBadge}</td>
                    <td>${shiftBadge}</td>
                    <td>${statusBadge}</td>
                    <td>${createdAt}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-sm btn-outline-primary btn-edit-user me-1" title="Edit" 
                                data-user="${encodeURIComponent(JSON.stringify({ id: user.id, name: user.name, email: user.email, role: user.role }))}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-user me-1" title="Delete" 
                                data-id="${user.id}" data-name="${escapeHtml(user.name)}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Update pagination info text
    function updatePaginationInfo(pagination) {
        paginationInfo.textContent = `Showing ${pagination.from}-${pagination.to} of ${pagination.total}`;
    }

    // Render pagination controls
    function renderPaginationControls(pagination) {
        const totalPages = Math.ceil(pagination.total / pagination.per_page);
        const currentPage = pagination.current_page;

        if (totalPages <= 1) {
            paginationControls.innerHTML = '';
            return;
        }

        let html = '';

        // Previous button
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        // Next button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;

        paginationControls.innerHTML = html;
    }

    // Update sort icons
    function updateSortIcons() {
        sortableHeaders.forEach(header => {
            const icon = header.querySelector('.sort-icon');
            header.classList.remove('asc', 'desc');
            icon.className = 'bi bi-arrow-down-up ms-1 text-muted sort-icon';

            if (header.dataset.sort === currentState.sortBy) {
                header.classList.add(currentState.sortOrder);
                icon.className = `bi bi-arrow-${currentState.sortOrder === 'asc' ? 'up' : 'down'} ms-1 sort-icon`;
            }
        });
    }


    // Search with debounce
    if (searchInput) {
        searchInput.addEventListener('input', debounce(async function (e) {
            currentState.search = e.target.value;
            currentState.page = 1;
            await fetchUsers();
        }, 300));
    }

    // Filter change handlers
    if (roleFilter) {
        roleFilter.addEventListener('change', function () {
            currentState.role = this.value;
            currentState.page = 1;
            fetchUsers();
        });
    }

    if (shiftFilter) {
        shiftFilter.addEventListener('change', function () {
            currentState.shift = this.value;
            currentState.page = 1;
            fetchUsers();
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', function () {
            currentState.status = this.value;
            currentState.page = 1;
            fetchUsers();
        });
    }

    // Clear filters
    // if (clearFiltersBtn) {
    //     clearFiltersBtn.addEventListener('click', function () {
    //         currentState = {
    //             search: '',
    //             role: '',
    //             shift: '',
    //             status: '',
    //             sortBy: 'created_at',
    //             sortOrder: 'desc',
    //             page: 1,
    //             perPage: 10
    //         };
    //         searchInput.value = '';
    //         roleFilter.value = '';
    //         shiftFilter.value = '';
    //         statusFilter.value = '';
    //         updateSortIcons();
    //         fetchUsers();
    //     });
    // }

    // Sortable headers
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const sortField = this.dataset.sort;

            if (currentState.sortBy === sortField) {
                currentState.sortOrder = currentState.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentState.sortBy = sortField;
                currentState.sortOrder = 'asc';
            }

            updateSortIcons();
            fetchUsers();
        });
    });

    // Pagination click handler
    if (paginationControls) {
        paginationControls.addEventListener('click', function (e) {
            e.preventDefault();
            const target = e.target.closest('[data-page]');
            if (target && !target.parentElement.classList.contains('disabled')) {
                currentState.page = parseInt(target.dataset.page);
                fetchUsers();
            }
        });
    }

    // Initial fetch
    fetchUsers();

    // ============ EDIT USER FUNCTIONALITY ============

    const editUserModalElement = document.getElementById('editUserModal');
    const editUserForm = document.getElementById('editUserForm');
    const editUserModal = new bootstrap.Modal(editUserModalElement);
    const editSubmitBtn = document.getElementById('edit-submit');

    const editNameInput = document.getElementById('editUserName');
    const editEmailInput = document.getElementById('editUserEmail');
    const editRoleInput = document.getElementById('editUserRole');
    const editUserIdInput = document.getElementById('editUserId');

    function validateEditName() {
        const value = editNameInput.value.trim();
        if (!value) {
            showError(editNameInput, 'Full name is required');
            return false;
        } else if (value.length < 3) {
            showError(editNameInput, 'Name must be at least 3 characters');
            return false;
        }
        clearError(editNameInput);
        return true;
    }

    function validateEditEmail() {
        const value = editEmailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!value) {
            showError(editEmailInput, 'Email address is required');
            return false;
        } else if (!emailRegex.test(value)) {
            showError(editEmailInput, 'Please enter a valid email address');
            return false;
        }
        clearError(editEmailInput);
        return true;
    }

    function validateEditRole() {
        if (!editRoleInput.value) {
            showError(editRoleInput, 'Please select a role');
            return false;
        }
        clearError(editRoleInput);
        return true;
    }

    function clearEditErrors() {
        const inputs = editUserForm.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => clearError(input));
    }

    if (editNameInput) editNameInput.addEventListener('input', validateEditName);
    if (editEmailInput) editEmailInput.addEventListener('input', validateEditEmail);
    if (editRoleInput) editRoleInput.addEventListener('change', validateEditRole);

    // Edit button click handler (event delegation)
    usersTableBody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-user');
        if (editBtn) {
            const userData = JSON.parse(decodeURIComponent(editBtn.dataset.user));
            editUserIdInput.value = userData.id;
            editNameInput.value = userData.name;
            editEmailInput.value = userData.email;
            editRoleInput.value = userData.role;
            clearEditErrors();
            editUserModal.show();
        }
    });

    // Edit form submission
    if (editUserForm) {
        editUserForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const isNameValid = validateEditName();
            const isEmailValid = validateEditEmail();
            const isRoleValid = validateEditRole();

            if (!isNameValid || !isEmailValid || !isRoleValid) {
                return;
            }

            const userId = editUserIdInput.value;
            const data = {
                name: editNameInput.value.trim(),
                email: editEmailInput.value.trim(),
                role: editRoleInput.value
            };

            try {
                editSubmitBtn.disabled = true;
                editSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

                const response = await fetch(`/admin/update-user/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.status === 'error') {
                    if (result.errors?.csrf) {
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.errors.csrf);
                    }
                    if (result.errors?.errors) {
                        Object.entries(result.errors.errors).forEach(([field, message]) => {
                            const input = editUserForm.querySelector(`[name="${field}"]`);
                            if (input) showError(input, message);
                        });
                    } else {
                        showAlert(result.message, 'error');
                    }
                    return;
                }

                if (result.data?.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.data.csrf);
                }

                showAlert(result.message, 'success');
                editUserModal.hide();
                fetchUsers();

            } catch (error) {
                console.error('Error:', error);
                showAlert('An error occurred', 'error');
            } finally {
                editSubmitBtn.disabled = false;
                editSubmitBtn.innerHTML = 'Update User';
            }
        });
    }

    // ============ DELETE USER FUNCTIONALITY ============

    const deleteUserModalElement = document.getElementById('deleteUserModal');
    const deleteUserModal = new bootstrap.Modal(deleteUserModalElement);
    const deleteUserIdInput = document.getElementById('deleteUserId');
    const deleteUserNameSpan = document.getElementById('deleteUserName');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Delete button click handler (event delegation)
    usersTableBody.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.btn-delete-user');
        if (deleteBtn) {
            deleteUserIdInput.value = deleteBtn.dataset.id;
            deleteUserNameSpan.textContent = deleteBtn.dataset.name;
            deleteUserModal.show();
        }
    });

    // Confirm delete
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function () {
            const userId = deleteUserIdInput.value;

            try {
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

                const response = await fetch(`/admin/delete-user/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.data?.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.data.csrf);
                }

                if (result.status === 'error') {
                    showAlert(result.message, 'error');
                    return;
                }

                showAlert(result.message, 'success');
                deleteUserModal.hide();
                fetchUsers();

            } catch (error) {
                console.error('Error:', error);
                showAlert('An error occurred', 'error');
            } finally {
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML = 'Delete';
            }
        });
    }

});
