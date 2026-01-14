function showAlert(message, type) {
    const alertBox = document.getElementById("custom-alert");
    alertBox.innerHTML = message;
    alertBox.className = "alert-box alert-" + type;
    alertBox.style.display = "block";

    setTimeout(() => {
        alertBox.style.display = "none";
    }, 3500);
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