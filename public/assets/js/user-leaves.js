document.addEventListener('DOMContentLoaded', function () {
    const toDateContainer = document.getElementById('toDateContainer');
    const timeContainer = document.getElementById('timeContainer');
    const leaveType = document.getElementById("leaveType");
    const leaveDuration = document.getElementById("leaveDuration");

    fetchLeaveTypes();

    async function fetchLeaveTypes() {
        await fetch('/user/leave-types')
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    showLeaveTypes(data.data);
                }
                else {
                    showAlert(data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert("Error fetching data", "error");
            });
    }

    function showLeaveTypes(data) {
        if (data.length === 0) {
            leaveType.innerHTML = `<option value="" selected disabled>No leaves available</option>`;
            return;
        }

        leaveType.innerHTML = `<option value="" selected disabled>Select Leave Type</option>`;

        data.forEach(item => {
            leaveType.innerHTML += `
                <option value="${item.id}">
                    ${item.name}
                </option>
            `;
        });
    }

    leaveDuration.addEventListener('change', function () {
        if (this.value === 'short') {
            toDateContainer.classList.add('d-none');
            timeContainer.classList.remove('d-none');
        } else if (this.value === 'half') {
            toDateContainer.classList.add('d-none');
            timeContainer.classList.add('d-none');
        } else {
            toDateContainer.classList.remove('d-none');
            timeContainer.classList.add('d-none');
        }
    });

    // Form elements
    const applyLeaveForm = document.getElementById('applyLeaveForm');
    const fromDateInput = applyLeaveForm.querySelector('input[type="date"]');
    const toDateInput = toDateContainer.querySelector('input[type="date"]');
    const startTimeInput = timeContainer.querySelector('input[type="time"]:first-of-type');
    const endTimeInput = timeContainer.querySelector('input[type="time"]:last-of-type');
    const reasonTextarea = applyLeaveForm.querySelector('textarea');
    const submitBtn = applyLeaveForm.querySelector('button[type="button"]');
    const applyLeaveModal = document.getElementById('applyLeaveModal');
    const modalInstance = bootstrap.Modal.getOrCreateInstance(applyLeaveModal);

    submitBtn.addEventListener('click', submitLeaveForm);

    // Clear errors when modal opens
    applyLeaveModal.addEventListener('show.bs.modal', function () {
        clearAllErrors();
        applyLeaveForm.reset();
        toDateContainer.classList.remove('d-none');
        timeContainer.classList.add('d-none');
    });

    function clearAllErrors() {
        [leaveType, leaveDuration, fromDateInput, toDateInput, startTimeInput, endTimeInput, reasonTextarea].forEach(input => {
            if (input) clearError(input);
        });
    }

    function validateForm() {
        let isValid = true;
        clearAllErrors();

        // Validate leave type
        if (!leaveType.value) {
            showError(leaveType, 'Please select a leave type');
            isValid = false;
        }

        // Validate leave duration
        if (!leaveDuration.value) {
            showError(leaveDuration, 'Please select leave duration');
            isValid = false;
        }

        // Validate from date
        if (!fromDateInput.value) {
            showError(fromDateInput, 'Please select from date');
            isValid = false;
        } else {
            const today = new Date().toISOString().split('T')[0];
            if (fromDateInput.value < today) {
                showError(fromDateInput, 'From date cannot be in the past');
                isValid = false;
            }
        }

        // Validate to date for full day leave
        if (leaveDuration.value === 'full' && !toDateInput.value) {
            showError(toDateInput, 'Please select to date');
            isValid = false;
        } else if (leaveDuration.value === 'full' && fromDateInput.value && toDateInput.value) {
            if (toDateInput.value < fromDateInput.value) {
                showError(toDateInput, 'To date cannot be before from date');
                isValid = false;
            }
        }

        // Validate time for short leave
        if (leaveDuration.value === 'short') {
            if (!startTimeInput.value) {
                showError(startTimeInput, 'Please select start time');
                isValid = false;
            }
            if (!endTimeInput.value) {
                showError(endTimeInput, 'Please select end time');
                isValid = false;
            }
            if (startTimeInput.value && endTimeInput.value && endTimeInput.value <= startTimeInput.value) {
                showError(endTimeInput, 'End time must be after start time');
                isValid = false;
            }
        }
        if (!reasonTextarea.value) {
            showError(reasonTextarea, 'Please enter reason');
            isValid = false;
        }

        return isValid;
    }

    async function submitLeaveForm() {
        if (!validateForm()) {
            return;
        }

        const formData = {
            leave_type_id: leaveType.value,
            type: leaveDuration.value,
            start_date: fromDateInput.value,
            end_date: leaveDuration.value === 'full' ? toDateInput.value : null,
            reason: reasonTextarea.value.trim()
        };

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch('/user/submit-leave', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });

            const res = await response.json();


            if (res.status === 'success') {
                if (res.data.csrf_token) {
                    updateCSRFToken(res.data.csrf_token);
                }
                showAlert(res.message || 'Leave request submitted successfully', 'success');
                modalInstance.hide();
                applyLeaveForm.reset();
                // Refresh the leave history table
                fetchLeaves();
            } else {
                if (res.errors.errors) {
                    if (res.errors?.csrf_token) {
                        updateCSRFToken(res.errors.csrf_token);
                    }
                    // Handle validation errors
                    Object.keys(res.errors.errors).forEach(field => {
                        const errorMsg = res.errors.errors[field];
                        if (field === 'leave_type_id') showError(leaveType, errorMsg);
                        else if (field === 'type') showError(leaveDuration, errorMsg);
                        else if (field === 'start_date') showError(fromDateInput, errorMsg);
                        else if (field === 'end_date') showError(toDateInput, errorMsg);
                        else if (field === 'start_time') showError(startTimeInput, errorMsg);
                        else if (field === 'end_time') showError(endTimeInput, errorMsg);
                        else if (field === 'reason') showError(reasonTextarea, errorMsg);
                    });
                } else {
                    if (res.errors?.csrf_token) {
                        updateCSRFToken(res.errors.csrf_token);
                    }
                    showAlert(res.message || 'Failed to submit leave request', 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred while submitting the request', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Request';
        }
    }

// ------fetch leaves ----
    let currentState = {
        search: '',
        status: 'pending',
        sortOrder: 'desc',
        page: 1,
        perPage: 5
    };

    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const leavesTableBody = document.getElementById('leavesTableBody');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const sortAppliedOn = document.getElementById('sortAppliedOn');

    // Initial fetch
    fetchLeaves();

    async function fetchLeaves() {
        try {
            const params = new URLSearchParams({
                search: currentState.search,
                status: currentState.status,
                sort_order: currentState.sortOrder,
                page: currentState.page,
                per_page: currentState.perPage
            });

            leavesTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading leaves...
                    </td>
                </tr>
            `;

            const response = await fetch(`/user/fetch-leaves?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                renderLeavesTable(result.data.leaves);
                updatePaginationInfo(result.data.pagination);
                renderPaginationControls(result.data.pagination);
            } else {
                showAlert(result.message || 'Failed to fetch leaves', 'error');
                leavesTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-exclamation-circle me-2"></i> Failed to load leaves
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Fetch error:', error);
            leavesTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-exclamation-circle me-2"></i> Error loading leaves
                    </td>
                </tr>
            `;
        }
    }

    function renderLeavesTable(leaves) {
        if (!leaves || leaves.length === 0) {
            leavesTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox me-2"></i> No leave requests found
                    </td>
                </tr>
            `;
            return;
        }

        leavesTableBody.innerHTML = leaves.map(leave => {
            // Duration display
            const durationText = leave.type === 'full' ? 'Full Day' :
                leave.type === 'half' ? 'Half Day' : 'Short Leave';

            // Date range display
            let dateRange = '';
            if (leave.start_date) {
                const startDate = formatDate(leave.start_date);
                if (leave.end_date && leave.end_date !== leave.start_date) {
                    dateRange = `${startDate} - ${formatDate(leave.end_date)}`;
                } else {
                    dateRange = startDate;
                }
            }

            // Status badge
            let statusBadge = '';
            switch (leave.status) {
                case 'pending':
                    statusBadge = '<span class="badge rounded-pill bg-warning text-dark px-3">Pending</span>';
                    break;
                case 'approved':
                    statusBadge = '<span class="badge rounded-pill bg-success text-white px-3">Approved</span>';
                    break;
                case 'rejected':
                    statusBadge = '<span class="badge rounded-pill bg-danger text-white px-3">Rejected</span>';
                    break;
                default:
                    statusBadge = `<span class="badge rounded-pill bg-secondary px-3">${leave.status}</span>`;
            }

            // Applied on date
            const appliedOn = formatDate(leave.created_at);

            return `
                <tr>
                    <td class="small">
                     ${escapeHtml(leave.leave_type_name )}
                    </td>
                    <td class="small">${escapeHtml(durationText)}</td>
                    <td class="small">${dateRange}</td>
                    <td class="text-muted small" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(leave.reason || '')}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end pe-4 text-muted small">${appliedOn}</td>
                </tr>
            `;
        }).join('');
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function updatePaginationInfo(pagination) {
        paginationInfo.textContent = `Showing ${pagination.from}-${pagination.to} of ${pagination.total}`;
    }

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

    function updateSortIcon() {
        const icon = sortAppliedOn.querySelector('.sort-icon');
        icon.className = `bi bi-arrow-${currentState.sortOrder === 'asc' ? 'up' : 'down'} ms-1 sort-icon`;
    }

    // ============ EVENT LISTENERS ============

    // Search with debounce
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function (e) {
            currentState.search = e.target.value;
            currentState.page = 1;
            fetchLeaves();
        }, 300));
    }

    // Status filter
    if (statusFilter) {
        statusFilter.addEventListener('change', function () {
            currentState.status = this.value;
            currentState.page = 1;
            fetchLeaves();
        });
    }

    // Sort by Applied On
    if (sortAppliedOn) {
        sortAppliedOn.addEventListener('click', function () {
            currentState.sortOrder = currentState.sortOrder === 'asc' ? 'desc' : 'asc';
            updateSortIcon();
            fetchLeaves();
        });
    }

    // Pagination click handler
    if (paginationControls) {
        paginationControls.addEventListener('click', function (e) {
            e.preventDefault();
            const target = e.target.closest('[data-page]');
            if (target && !target.parentElement.classList.contains('disabled')) {
                currentState.page = parseInt(target.dataset.page);
                fetchLeaves();
            }
        });
    }

});
