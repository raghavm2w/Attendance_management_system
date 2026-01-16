function showAlert(message, type = 'info') {
    const alertBox = document.getElementById("custom-alert");
    if (!alertBox) return;

    // Map type to icons
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-octagon-fill',
        danger: 'bi-exclamation-octagon-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };

    const icon = icons[type] || icons.info;
    const alertType = type === 'error' ? 'danger' : type;

    alertBox.innerHTML = `
        <i class="bi ${icon} alert-icon"></i>
        <div class="alert-message">${message}</div>
    `;

    // Remove any existing alert classes
    alertBox.className = "alert-box show alert-" + alertType;

    // Play a subtle entry sound if allowed by browser (optional, omitted here)

    setTimeout(() => {
        alertBox.classList.remove('show');
    }, 4500);
}
function showError(input, message) {
    input.classList.add('is-invalid');
    const parent = input.closest('.input-group') || input.parentElement;
    let feedback = parent.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        parent.appendChild(feedback);
    }
    feedback.textContent = message;
    feedback.style.display = 'block';
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

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
}


function updateCSRFToken(token) {
    if (token) {
        document.querySelector('meta[name="csrf-token"]').setAttribute('content', token);
    }
}