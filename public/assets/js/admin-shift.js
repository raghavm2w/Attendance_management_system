document.addEventListener('DOMContentLoaded', function () {

    // State management
    let currentState = {
        search: '',
        sortBy: 'created_at',
        sortOrder: 'desc',
        page: 1,
        perPage: 5
    };

    // Elements
    const searchInput = document.getElementById('searchInput');
    const shiftsTableBody = document.getElementById('shiftsTableBody');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const sortableHeaders = document.querySelectorAll('.sortable');

    // Modals
    const addShiftModalElement = document.getElementById('addShiftModal');
    const addShiftModal = new bootstrap.Modal(addShiftModalElement);
    const addShiftForm = document.getElementById('addShiftForm');

    const editShiftModalElement = document.getElementById('editShiftModal');
    const editShiftModal = new bootstrap.Modal(editShiftModalElement);
    const editShiftForm = document.getElementById('editShiftForm');

    const deleteShiftModalElement = document.getElementById('deleteShiftModal');
    const deleteShiftModal = new bootstrap.Modal(deleteShiftModalElement);

    function showError(input, message) {
        input.classList.add('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        }
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = '';
        }
    }

    // function showAlert(message, type = 'success') {
    //     const alertDiv = document.createElement('div');
    //     alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    //     alertDiv.style.zIndex = '9999';
    //     alertDiv.innerHTML = `
    //         ${message}
    //         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //     `;
    //     document.body.appendChild(alertDiv);
    //     setTimeout(() => alertDiv.remove(), 3000);
    // }


    // Format time (13:00 -> 01:00 PM)
    function formatTime(timeString) {
        if (!timeString) return '-';
        const [hours, minutes] = timeString.split(':');
        const date = new Date();
        date.setHours(hours);
        date.setMinutes(minutes);
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    // ============ FETCH & RENDER ============

    async function fetchShifts() {
        try {
            const params = new URLSearchParams({
                search: currentState.search,
                sort_by: currentState.sortBy,
                sort_order: currentState.sortOrder,
                page: currentState.page,
                per_page: currentState.perPage
            });

            shiftsTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading shifts...
                    </td>
                </tr>
            `;

            const response = await fetch(`/admin/fetch-shifts?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                renderShiftsTable(result.data.shifts);
                updatePaginationInfo(result.data.pagination);
                renderPaginationControls(result.data.pagination);
            } else {
                showAlert(result.message || 'Failed to fetch shifts', 'error');
                shiftsTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-exclamation-circle me-2"></i> Failed to load shifts
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Fetch error:', error);
            shiftsTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-exclamation-circle me-2"></i> Error loading shifts
                    </td>
                </tr>
            `;
        }
    }

    function renderShiftsTable(shifts) {
        if (!shifts || shifts.length === 0) {
            shiftsTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        No shifts found
                    </td>
                </tr>
            `;
            return;
        }

        shiftsTableBody.innerHTML = shifts.map(shift => {
            const shiftData = encodeURIComponent(JSON.stringify(shift));

            return `
                <tr>
                    <td class="fw-medium">${escapeHtml(shift.type)}</td>
                    <td>${formatTime(shift.start_time)}</td>
                    <td>${formatTime(shift.end_time)}</td>
                    <td>${shift.grace_time} Mins</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary btn-edit-shift me-1" 
                                data-shift="${shiftData}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-delete-shift" 
                                data-id="${shift.id}" data-type="${escapeHtml(shift.type)}" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updatePaginationInfo(pagination) {
        const { from, to, total } = pagination;
        paginationInfo.textContent = `Showing ${from}-${to} of ${total}`;
    }

    function renderPaginationControls(pagination) {
        const { current_page, total, per_page } = pagination;
        const totalPages = Math.ceil(total / per_page);

        if (totalPages <= 1) {
            paginationControls.innerHTML = '';
            return;
        }

        let html = '';

        // Previous button
        html += `
            <li class="page-item ${current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current_page - 1}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers logic (smart ellipsis)
        const startPage = Math.max(1, current_page - 2);
        const endPage = Math.min(totalPages, current_page + 2);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === current_page ? 'active' : ''}">
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
            <li class="page-item ${current_page === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current_page + 1}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;

        paginationControls.innerHTML = html;
    }



    // Search event listener
    searchInput.addEventListener('input', debounce(function (e) {
        currentState.search = e.target.value.trim();
        currentState.page = 1; // Reset to first page
        fetchShifts();
    }, 300));

    // Sorting
    sortableHeaders.forEach(th => {
        th.addEventListener('click', () => {
            const sortBy = th.dataset.sort;

            // Toggle order if clicking same column, otherwise default to asc
            if (currentState.sortBy === sortBy) {
                currentState.sortOrder = currentState.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentState.sortBy = sortBy;
                currentState.sortOrder = 'asc';
            }

            // Update UI
            sortableHeaders.forEach(header => {
                header.classList.remove('asc', 'desc');
                const icon = header.querySelector('.sort-icon');
                if (icon) icon.className = 'bi bi-arrow-down-up ms-1 text-muted sort-icon';
            });

            th.classList.add(currentState.sortOrder);
            const icon = th.querySelector('.sort-icon');
            if (icon) {
                icon.className = currentState.sortOrder === 'asc'
                    ? 'bi bi-arrow-up ms-1 text-primary sort-icon'
                    : 'bi bi-arrow-down ms-1 text-primary sort-icon';
            }

            fetchShifts();
        });
    });

    // Pagination
    paginationControls.addEventListener('click', function (e) {
        e.preventDefault();
        const link = e.target.closest('.page-link');
        if (!link || link.parentElement.classList.contains('disabled')) return;

        const page = parseInt(link.dataset.page);
        if (page) {
            currentState.page = page;
            fetchShifts();
        }
    });

    // ============ ADD SHIFT ============

    document.getElementById('btnAddShift').addEventListener('click', () => {
        addShiftForm.reset();
        addShiftForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        addShiftModal.show();
    });

    function validateShiftForm(form) {
        let isValid = true;

        const typeInput = form.querySelector('[name="type"]');
        const startTimeInput = form.querySelector('[name="start_time"]');
        const endTimeInput = form.querySelector('[name="end_time"]');
        const graceTimeInput = form.querySelector('[name="grace_time"]');

        // Name validation
        if (!typeInput.value.trim()) {
            showError(typeInput, 'Shift name is required');
            isValid = false;
        } else if (typeInput.value.trim().length < 2) {
            showError(typeInput, 'Shift name must be at least 2 characters');
            isValid = false;
        } else {
            clearError(typeInput);
        }

        // Time validation
        if (!startTimeInput.value) {
            showError(startTimeInput, 'Start time is required');
            isValid = false;
        } else {
            clearError(startTimeInput);
        }

        if (!endTimeInput.value) {
            showError(endTimeInput, 'End time is required');
            isValid = false;
        } else {
            clearError(endTimeInput);
        }

        // Grace time validation
        const graceTime = parseInt(graceTimeInput.value);
        if (graceTimeInput.value === '' || isNaN(graceTime)) {
            showError(graceTimeInput, 'Grace time is required');
            isValid = false;
        } else if (graceTime < 0 || graceTime > 120) {
            showError(graceTimeInput, 'Grace time must be between 0 and 120 minutes');
            isValid = false;
        } else {
            clearError(graceTimeInput);
        }

        return isValid;
    }

    addShiftForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!validateShiftForm(this)) return;

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch('/admin/add-shift', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === 'success') {
                if (result.data?.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.data.csrf);
                }
                showAlert(result.message, 'success');
                addShiftModal.hide();
                fetchShifts();
            } else {
                if (result.errors?.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.errors.csrf);
                }
                if (result.errors) {
                    // Handle server-side validation errors
                    if (result.errors.errors) {
                        Object.entries(result.errors.errors).forEach(([field, message]) => {
                            const input = addShiftForm.querySelector(`[name="${field}"]`);
                            if (input) showError(input, message);
                        });
                    } else {
                        showAlert(result.message, 'error');
                    }
                } else {
                    showAlert(result.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred while creating shift', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // ============ EDIT SHIFT ============

    const editIdInput = document.getElementById('editShiftId');
    const editNameInput = document.getElementById('editShiftName');
    const editStartTimeInput = document.getElementById('editStartTime');
    const editEndTimeInput = document.getElementById('editEndTime');
    const editGraceTimeInput = document.getElementById('editGraceTime');

    shiftsTableBody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-shift');
        if (editBtn) {
            const shiftData = JSON.parse(decodeURIComponent(editBtn.dataset.shift));
            editIdInput.value = shiftData.id;
            editNameInput.value = shiftData.type;

            // Format times for input (HH:mm:ss -> HH:mm)
            if (shiftData.start_time) {
                editStartTimeInput.value = shiftData.start_time.substring(0, 5);
            }
            if (shiftData.end_time) {
                editEndTimeInput.value = shiftData.end_time.substring(0, 5);
            }

            editGraceTimeInput.value = shiftData.grace_time;

            // Clear errors
            editShiftForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            editShiftModal.show();
        }
    });

    editShiftForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!validateShiftForm(this)) return;

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        const shiftId = editIdInput.value;

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch(`/admin/update-shift/${shiftId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === 'success') {
                if (result.data?.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.data.csrf);
                }
                showAlert(result.message, 'success');
                editShiftModal.hide();
                fetchShifts();
            } else {
                if (result.errors?.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.errors.csrf);
                }
                if (result.errors && result.errors.errors) {
                    Object.entries(result.errors.errors).forEach(([field, message]) => {
                        const input = editShiftForm.querySelector(`[name="${field}"]`);
                        if (input) showError(input, message);
                    });
                } else {
                    showAlert(result.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred while updating shift', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // ============ DELETE SHIFT ============

    const deleteIdInput = document.getElementById('deleteShiftId');
    const deleteNameSpan = document.getElementById('deleteShiftName');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    shiftsTableBody.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.btn-delete-shift');
        if (deleteBtn) {
            deleteIdInput.value = deleteBtn.dataset.id;
            // deleteNameSpan.textContent = deleteBtn.dataset.type;
            deleteShiftModal.show();
        }
    });

    confirmDeleteBtn.addEventListener('click', async function () {
        const shiftId = deleteIdInput.value;
        const originalText = this.innerHTML;

        try {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

            const response = await fetch(`/admin/delete-shift/${shiftId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                if (result.data?.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.data.csrf);
                }
                showAlert(result.message, 'success');
                deleteShiftModal.hide();
                fetchShifts();
            } else {
                if (result.data?.csrf) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.data.csrf);
                }
                showAlert(result.message || 'Failed to delete shift', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred while deleting shift', 'error');
        } finally {
            this.disabled = false;
            this.innerHTML = originalText;
        }
    });

    // Initial fetch
    fetchShifts();
});
