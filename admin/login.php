<?php
// admin/login.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error_message = '';

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Username dan password harus diisi.';
    } else {
        // Check credentials
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect('dashboard.php');
        } else {
            $error_message = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CHIBOR</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        }

        body {
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .login-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .form-floating>.form-control {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-floating>.form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .alert {
            border-radius: 12px;
            border: none;
        }

        .back-to-site {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .back-to-site:hover {
            color: #f1f5f9;
        }

        .login-footer {
            background: #f8f9fa;
            padding: 1rem 2rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
            }

            .login-header,
            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Back to Site Link -->
    <a href="../" class="back-to-site">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Website
    </a>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="login-header">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-3x"></i>
                        </div>
                        <h3 class="fw-bold mb-2">Admin Panel</h3>
                        <p class="mb-0 opacity-75">PT. Irgha Reksa Jasa - CHIBOR</p>
                    </div>

                    <div class="login-body">
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?= h($error_message) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="loginForm">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= h($_POST['username'] ?? '') ?>" required>
                                <label for="username">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Admin Panel
                            </button>
                        </form>

                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Gunakan kredensial admin yang telah diberikan
                            </small>
                        </div>
                    </div>

                    <div class="login-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small>&copy; <?= date('Y') ?> PT. Irgha Reksa Jasa</small>
                            <small>v1.0</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const submitButton = loginForm.querySelector('button[type="submit"]');

            loginForm.addEventListener('submit', function(e) {
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
                submitButton.disabled = true;

                // Re-enable button after 3 seconds if form doesn't redirect
                setTimeout(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }, 3000);
            });

            // Auto-focus username field
            document.getElementById('username').focus();

            // Show/hide password
            const passwordField = document.getElementById('password');
            const showPasswordBtn = document.createElement('button');
            showPasswordBtn.type = 'button';
            showPasswordBtn.className = 'btn btn-link position-absolute end-0 top-50 translate-middle-y';
            showPasswordBtn.style.zIndex = '10';
            showPasswordBtn.innerHTML = '<i class="fas fa-eye"></i>';

            passwordField.parentNode.style.position = 'relative';
            passwordField.parentNode.appendChild(showPasswordBtn);

            showPasswordBtn.addEventListener('click', function() {
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordField.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        });
    </script>
</body>

</html>