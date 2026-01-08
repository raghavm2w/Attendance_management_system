<?= $this->include('partials/header') ?>

<div class="auth-wrapper d-flex justify-content-center align-items-center">
    <div class="auth-card card shadow-sm border-0 p-4">
        <h3 class="fw-bold mb-1">Welcome Back</h3>
        <p class="text-muted mb-4">Please enter your details to access your account.</p>

        <form id="loginForm">
            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" id="login-email" name="email" class="form-control" placeholder="name@company.com" required>
                </div>
                    <small class="form-error"></small>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label class="form-label">Password</label>
                    <a href="#" class="small text-decoration-none">Forgot Password?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" id="login-pass"name="password" class="form-control" required>
                    <span id="eye-btn" class="input-group-text bg-white">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
                    <small class="form-error"></small>
            </div>

            <!-- Button -->
            <button type="submit" class="btn btn-primary w-100 py-2 mt-3">
                Sign In
            </button>
              <span class="loader hidden"></span>

        </form>
    </div>
</div>

<script src="<?= base_url('assets/js/auth.js') ?>">
</script>
<?= $this->include('partials/footer') ?>
