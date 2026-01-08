const loginForm = document.getElementById("loginForm");

document.getElementById("eye-btn").addEventListener("click",(e) => {
    togglePassword(e.currentTarget)
});
function showLoader(show) {
  document.querySelector('.loader').classList.toggle('hidden', !show);
}
function togglePassword(eye) {
    const input = eye.previousElementSibling;
    const icon = eye.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    }
}
const validateLoginEmail = () => {
  const email = document.getElementById('login-email');
  const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

  if (!regex.test(email.value.trim())) {
    showError(email, 'Enter a valid email address');
    return false;
  }
  clearError(email);
  return true;
};

const validateLoginPassword = () => {
  const password = document.getElementById('login-pass');

  if (password.value.length < 6) {
    showError(password, 'Password must be at least 6 characters');
    return false;
  }
  clearError(password);
  return true;
};
if(loginForm){

    document.getElementById('login-email').addEventListener('change', validateLoginEmail);
    document.getElementById('login-pass').addEventListener('change', validateLoginPassword);
    loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!(validateLoginEmail() && validateLoginPassword())) return;

    const loginData = {
      email: loginForm.email.value.trim(),
      password: loginForm.password.value
    }

    fetch('/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(loginData)
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === "error") {
          showAlert(data.message, "error");
          loginForm.reset();
          return;
        }
        if ( data.data.role === 'admin') {
          window.location.href = "/admin/dash";
        } else {
          window.location.href = "/";
        }
      })
      .catch(err => {
        console.error("Fetch error:", err);
        showAlert("An error occurred while login user", "error");
      });
  });
}







const showError = (input, message) => {
  const error = input.closest(".mb-3").querySelector('.form-error');
  error.textContent = message;
  input.classList.add('invalid');
};

const clearError = (input) => {
  const error = input.closest(".mb-3").querySelector('.form-error');
  error.textContent = '';
  input.classList.remove('invalid');
};