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
                if (data.status === "error") {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.errors.csrf);

                if (data.errors.errors) {
                    Object.entries(data.errors.errors).forEach(([field, message]) => {
                        const input = addUserForm[field];
                        if (input) {
                            showError(input, message);
                        }
                    });
          } else {
            showAlert(data.message, "error");
                addUserForm.reset();
          }
          return;
        }
         if (data.data.csrf) {
        document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.data.csrf);
      }
      showAlert(data.message, "success");
      addUserModal.hide();
      addUserForm.reset();
                    

                
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
    // Helper Functions
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

    
});
