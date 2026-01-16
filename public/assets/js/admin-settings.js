// Settings management (IP Address Section)
let currentIpPage = 1;
let ipSortBy = 'created_at';
let ipSortOrder = 'desc';
function clearIpErrors(form) {
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        clearError(input);
    });
}
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('ipsTableBody');
    const btnAddIp = document.getElementById('btnAddIp');
    const ipSearchInput = document.getElementById('ipSearchInput');
    const ipStatusFilter = document.getElementById('ipStatusFilter');
    const timezoneForm = document.getElementById('timezoneForm');

    if (btnAddIp) {
        btnAddIp.addEventListener('click', () => {
            const addIpForm = document.getElementById('addIpForm');
            addIpForm.reset();
            clearIpErrors(addIpForm);
            new bootstrap.Modal(document.getElementById('addIpModal')).show();
        });
    }

    if (ipSearchInput) {
        ipSearchInput.addEventListener('input', debounce(() => {
            currentIpPage = 1;
            fetchIps();
        }, 500));
    }

    if (ipStatusFilter) {
        ipStatusFilter.addEventListener('change', () => {
            currentIpPage = 1;
            fetchIps();
        });
    }

    // IP Form Submissions
    const addIpForm = document.getElementById('addIpForm');
    if (addIpForm) {
        addIpForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = {
                label: document.getElementById('ipLabel').value,
                ip_address: document.getElementById('ipAddress').value
            };
            submitIpData('/admin/settings/add-ip', formData, 'addIpModal', 'POST', this);
        });
    }

    const editIpForm = document.getElementById('editIpForm');
    if (editIpForm) {
        editIpForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const id = document.getElementById('editIpId').value;
            const formData = {
                label: document.getElementById('editIpLabel').value,
                ip_address: document.getElementById('editIpAddress').value
            };
            console.log(this);
            submitIpData(`/admin/settings/update-ip/${id}`, formData, 'editIpModal', 'POST', this);
        });
    }

    const confirmDeleteIpBtn = document.getElementById('confirmDeleteIpBtn');
    if (confirmDeleteIpBtn) {
        confirmDeleteIpBtn.addEventListener('click', function () {
            const id = document.getElementById('deleteIpId').value;
            submitIpData(`/admin/settings/delete-ip/${id}`, {}, 'deleteIpModal', 'POST', null);
        });
    }

    // Timezone Form Submission
    if (timezoneForm) {
        timezoneForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const timezone = document.getElementById('systemTimezone').value;

            fetch('/admin/settings/update-timezone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ timezone: timezone })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert(data.message, 'success');
                        updateCSRFToken(data.data.csrf);
                    } else {
                        showAlert(data.message, 'danger');
                        if (data.data && data.data.csrf) updateCSRFToken(data.data.csrf);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while saving timezone.', 'danger');
                });
        });
    }

    // Initial fetch
    if (tableBody) fetchIps();
});

function fetchIps() {
    const search = document.getElementById('ipSearchInput')?.value || '';
    const status = document.getElementById('ipStatusFilter')?.value || '1';

    const url = `/admin/settings/fetch-ips?search=${search}&status=${status}&page=${currentIpPage}&sort_by=${ipSortBy}&sort_order=${ipSortOrder}`;

    const tableBody = document.getElementById('ipsTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4 text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading IP addresses...
            </td>
        </tr>
    `;

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderIpsTable(data.data.ips);
                renderPagination(data.data.pagination);
            } else {
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${data.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to fetch data.</td></tr>`;
        });
}

function renderIpsTable(ips) {
    const tableBody = document.getElementById('ipsTableBody');
    if (ips.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No IP addresses found.</td></tr>`;
        return;
    }

    tableBody.innerHTML = ips.map(ip => `
        <tr>
            <td>${ip.label}</td>
            <td><code>${ip.ip_address}</code></td>
            <td>
                <span class="badge ${ip.is_active == 1 ? 'bg-success' : 'bg-danger'} text-white">
                    ${ip.is_active == 1 ? 'Active' : 'Deleted'}
                </span>
            </td>
            <td class="small text-muted">${ip.created_at}</td>
            <td>
                ${ip.is_active == 1 ? `
                    <button class="edit me-2" onclick="editIp(${ip.id}, '${ip.label}', '${ip.ip_address}')" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="delete" onclick="deleteIp(${ip.id})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                ` : `
                    <button class="text-success border-0 bg-transparent" onclick="restoreIp(${ip.id})" title="Restore">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                `}
            </td>
        </tr>
    `).join('');
}

function renderPagination(pagination) {
    const info = document.getElementById('ipPaginationInfo');
    const controls = document.getElementById('ipPaginationControls');

    if (info) info.textContent = `Showing ${pagination.from}-${pagination.to} of ${pagination.total}`;

    if (controls) {
        let html = '';
        const totalPages = Math.ceil(pagination.total / pagination.per_page);

        // Previous
        html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${pagination.current_page - 1})"><i class="bi bi-chevron-left"></i></a>
        </li>`;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                </li>`;
            } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${pagination.current_page === totalPages || totalPages === 0 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${pagination.current_page + 1})"><i class="bi bi-chevron-right"></i>
</a>
        </li>`;

        controls.innerHTML = html;
    }
}

function changePage(page) {
    event.preventDefault();
    currentIpPage = page;
    fetchIps();
}

function submitIpData(url, data, modalId, method = 'POST', form) {
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                showAlert(result.message, 'success');
                updateCSRFToken(result.data.csrf);
                if(modalId){
                bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                }
                if(form){
                form.reset();
                }
                fetchIps();
                return;
            } else {
                updateCSRFToken(result.data?.csrf || result.errors?.csrf);

                if (result.errors.errors) {
                    Object.entries(result.errors.errors).forEach(([field, message]) => {
                        console.log(form);
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            showError(input, message);
                        }
                    });
                } else {
                    showAlert(result.message, "error");
                    if(form){
                    form.reset();
                    }
                }
                return;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Something went wrong', error);
        });
}

function editIp(id, label, ip) {
    document.getElementById('editIpId').value = id;
    document.getElementById('editIpLabel').value = label;
    document.getElementById('editIpAddress').value = ip;
    clearIpErrors(document.getElementById('editIpForm'));

    new bootstrap.Modal(document.getElementById('editIpModal')).show();
}

function deleteIp(id) {
    document.getElementById('deleteIpId').value = id;
    new bootstrap.Modal(document.getElementById('deleteIpModal')).show();
}

function restoreIp(id) {
    if (confirm('Are you sure you want to restore this IP address?')) {
        submitIpData(`/admin/settings/restore-ip/${id}`, {}, null, 'POST', null);
    }
}
