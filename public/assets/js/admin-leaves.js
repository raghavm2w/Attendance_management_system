document.addEventListener('DOMContentLoaded', function () {
    // Current state management
    let currentState = {
        search: '',
        status: 'pending',
        applied_from: '',
        applied_to: '',
        leave_from: '',
        leave_to: '',
        sort_by: 'created_at',
        sort_order: 'desc',
        page: 1,
        per_page: 10
    };

    // DOM Elements
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const applied_from = document.getElementById('applied_from');
    const applied_to = document.getElementById('applied_to');
    const leave_from = document.getElementById('leave_from');
    const leave_to = document.getElementById('leave_to');
    const btnResetFilters = document.getElementById('btnResetFilters');

    const leavesTableBody = document.getElementById('leavesTableBody');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');

    const approveLeaveModal = new bootstrap.Modal(document.getElementById('approveLeaveModal'));
    const rejectLeaveModal = new bootstrap.Modal(document.getElementById('rejectLeaveModal'));
    const confirmApproveBtn = document.getElementById('confirmApproveBtn');
    const confirmRejectBtn = document.getElementById('confirmRejectBtn');

    const approveLeaveId = document.getElementById('approveLeaveId');
    const rejectLeaveId = document.getElementById('rejectLeaveId');

    // Initial Fetch
    fetchLeaves();

    // ============ Event Listeners ============

    // Search with debounce
    searchInput.addEventListener('input', debounce(function (e) {
        currentState.search = e.target.value;
        currentState.page = 1;
        fetchLeaves();
    }, 500));

    // Simple Filters
    statusFilter.addEventListener('change', function () {
        currentState.status = this.value;
        currentState.page = 1;
        fetchLeaves();
    });

    // Date Filters
    [applied_from, applied_to, leave_from, leave_to].forEach(el => {
        el.addEventListener('change', function () {
            currentState[this.id] = this.value;
            currentState.page = 1;
            fetchLeaves();
        });
    });

    // Reset Filters
    btnResetFilters.addEventListener('click', function () {
        searchInput.value = '';
        statusFilter.value = 'pending';
        applied_from.value = '';
        applied_to.value = '';
        leave_from.value = '';
        leave_to.value = '';

        currentState = {
            ...currentState,
            search: '',
            status: 'pending',
            applied_from: '',
            applied_to: '',
            leave_from: '',
            leave_to: '',
            page: 1
        };

        fetchLeaves();
    });

    // Sorting
    document.querySelectorAll('.sortable').forEach(th => {
        th.addEventListener('click', function () {
            const sortBy = this.dataset.sort;

            if (currentState.sort_by === sortBy) {
                currentState.sort_order = currentState.sort_order === 'asc' ? 'desc' : 'asc';
            } else {
                currentState.sort_by = sortBy;
                currentState.sort_order = 'asc';
            }

            // Update UI for sort
            document.querySelectorAll('.sortable').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            const icon = this.querySelector('.sort-icon');
            document.querySelectorAll('.sort-icon').forEach(i => i.className = 'bi bi-arrow-down-up ms-1 sort-icon');
            icon.className = `bi bi-arrow-${currentState.sort_order === 'asc' ? 'up' : 'down'} ms-1 sort-icon`;

            fetchLeaves();
        });
    });

    // Pagination Click
    paginationControls.addEventListener('click', function (e) {
        e.preventDefault();
        const link = e.target.closest('.page-link');
        if (!link || !link.dataset.page) return;

        const newPage = parseInt(link.dataset.page);
        if (newPage !== currentState.page) {
            currentState.page = newPage;
            fetchLeaves();
        }
    });

    // Action Handlers (Approve/Reject)
    leavesTableBody.addEventListener('click', function (e) {
        const approveBtn = e.target.closest('.btn-approve');
        const rejectBtn = e.target.closest('.btn-reject');

        if (approveBtn) {
            approveLeaveId.value = approveBtn.dataset.id;
            approveLeaveModal.show();
        }

        if (rejectBtn) {
            rejectLeaveId.value = rejectBtn.dataset.id;
            rejectLeaveModal.show();
        }
    });

    confirmApproveBtn.addEventListener('click', function () {
        handleAction('approve', approveLeaveId.value, approveLeaveModal, confirmApproveBtn);
    });

    confirmRejectBtn.addEventListener('click', function () {
        handleAction('reject', rejectLeaveId.value, rejectLeaveModal, confirmRejectBtn);
    });

    // ============ Core Functions ============

    async function fetchLeaves() {
        try {
            const params = new URLSearchParams(currentState).toString();
            leavesTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading leave requests...
                    </td>
                </tr>
            `;

            const response = await fetch(`/admin/fetch-leaves?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const res = await response.json();

            if (res.status === 'success') {
                renderTable(res.data.leaves);
                updatePagination(res.data.pagination);
            } else {
                showAlert(res.message || 'Error fetching leaves', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred while fetching data', 'error');
        }
    }

    function renderTable(leaves) {
        if (!leaves || leaves.length === 0) {
            leavesTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox me-2"></i> No leave requests found.
                    </td>
                </tr>
            `;
            return;
        }

        leavesTableBody.innerHTML = leaves.map(leave => {
            const dateRange = leave.type === 'full'
                ? `${formatDate(leave.start_date)} to ${formatDate(leave.end_date)}`
                : `${formatDate(leave.start_date)} (${leave.type === 'half' ? 'Half Day' : 'Short Leave'})`;

            const statusClass = leave.status === 'approved' ? 'bg-success' : (leave.status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
            const statusLabel = leave.status.charAt(0) ? leave.status.charAt(0).toUpperCase() + leave.status.slice(1) : leave.status;

            return `
                <tr>
                    <td>
                        <div class="fw-bold">${escapeHtml(leave.user_name)}</div>
                        <div class="text-muted small">${escapeHtml(leave.user_email)}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border extra-small">${escapeHtml(leave.leave_type_name)}</span></td>
                    <td class="small">${dateRange}</td>
                    <td class="small text-muted" title="${escapeHtml(leave.reason)}">
                        ${leave.reason ? (leave.reason.length > 30 ? escapeHtml(leave.reason.substring(0, 30)) + '...' : escapeHtml(leave.reason)) : 'N/A'}
                    </td>
                    <td><span class="badge ${statusClass} rounded-pill px-3 extra-small">${statusLabel}</span></td>
                    <td class="small text-muted">${formatDate(leave.created_at)}</td>
                    <td class="text-end">
                        ${leave.status === 'pending' ? `
                            <div class="btn-group">
                                <button class="btn btn-outline-success btn-sm btn-approve" data-id="${leave.id}" title="Approve">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm btn-reject" data-id="${leave.id}" title="Reject">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        ` : '<span class="text-muted small">Processed</span>'}
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function handleAction(action, id, modal, btn) {
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        try {
            const response = await fetch(`/admin/${action}-leave/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const res = await response.json();

            if (res.status === 'success') {
                showAlert(res.message, 'success');
                modal.hide();
                fetchLeaves();
            } else {
                showAlert(res.message || 'Action failed', 'error');
            }

            if (res.data && res.data.csrf) {
                updateCSRFToken(res.data.csrf);
            } else if (res.errors && res.errors.csrf) {
                updateCSRFToken(res.errors.csrf);
            }

        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred during the request', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    function updatePagination(pagination) {
        paginationInfo.textContent = `Showing ${pagination.from}-${pagination.to} of ${pagination.total}`;

        const totalPages = Math.ceil(pagination.total / pagination.per_page);
        if (totalPages <= 1) {
            paginationControls.innerHTML = '';
            return;
        }

        let html = '';

        // Prev
        html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}"><i class="bi bi-chevron-left"></i></a>
        </li>`;

        // Pages (Simplified for now, similar to users)
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${pagination.current_page === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}"><i class="bi bi-chevron-right"></i></a>
        </li>`;

        paginationControls.innerHTML = html;
    }

    // ============ Helpers ============
    function formatDate(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }
});
