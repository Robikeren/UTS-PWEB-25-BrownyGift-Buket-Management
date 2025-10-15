<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_POST) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $user = mysqli_fetch_assoc($query);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        
        if ($user['role'] == 'admin') {
            header('Location: admin/');
        } elseif ($user['role'] == 'customer') {
            header('Location: customer/');
        } elseif ($user['role'] == 'ekspedisi') {
            header('Location: ekspedisi/');
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BrownyGift</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Login Page Specific Styles */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem 1rem;
        }
        
        .login-card {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 800px; /* ← BESAR seperti dashboard */
            border: 1px solid #e5e7eb;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-header h1 {
            color: #8b5cf6;
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
        }
        
        .login-header p {
            color: #6b7280;
            font-size: 1.1rem;
            margin: 0;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            max-width: 500px; /* ← Lebar form di tengah card besar */
            margin: 0 auto;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: #374151;
            font-size: 1rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem 1.25rem; /* ← Padding lebih besar */
            border: 2px solid #e5e7eb;
            border-radius: 10px; /* ← Radius lebih besar */
            font-size: 1.1rem; /* ← Font lebih besar */
            transition: all 0.2s ease;
            box-sizing: border-box;
            height: 56px; /* ← Tinggi tetap */
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .login-actions {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: white;
            padding: 1.125rem 2.5rem; /* ← Padding lebih besar */
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            height: 56px; /* ← Tinggi sama dengan input */
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.3);
        }
        
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            padding: 1.25rem;
            border-radius: 10px;
            border: 1px solid #fecaca;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 500;
        }
        
        .login-decoration {
            position: absolute;
            top: 20%;
            right: 5%;
            opacity: 0.1;
            font-size: 8rem;
            z-index: -1;
        }
        
        @media (max-width: 768px) {
            .login-card {
                padding: 2rem;
                margin: 1rem;
                max-width: 90%;
            }
            
            .login-header h1 {
                font-size: 2rem;
            }
            
            .login-form {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Dekorasi background -->
            <div class="login-decoration">🌸</div>
            
            <div class="login-header">
                <h1>🌸 BrownyGift</h1>
                <p>Silakan login untuk mengakses sistem</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert-error">
                    <strong>❌ Login Gagal!</strong><br>
                    Username atau password yang Anda masukkan salah.
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">👤 Username</label>
                    <input 
                        type="text" 
                        id="username"
                        name="username" 
                        placeholder="Masukkan username Anda"
                        value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                        required
                        autocomplete="username"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">🔒 Password</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        placeholder="Masukkan password Anda"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <div class="login-actions">
                    <button type="submit" class="btn-login">
                        🚀 Masuk ke Sistem
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>