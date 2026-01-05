<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();

    if ($user && $user['password'] === $_POST['password']) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = strtoupper($user['role']);
        $_SESSION['franchise_id'] = $user['franchise_id'];

        if ($_SESSION['role'] === 'ADMIN') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: franchisé/dashboard.php');
        }
        exit;
    }

    $error = "Identifiants incorrects";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion – DRIV’N COOK</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
    }

    .login-card {
        background: #fff;
        padding: 2.5rem;
        border-radius: 1.25rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .login-card h2 {
        font-weight: 600;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .form-control {
        border-radius: .75rem;
        padding: .75rem 1rem;
    }

    .btn-login {
        border-radius: .75rem;
        padding: .75rem;
        font-size: 1rem;
        font-weight: 500;
    }

    .brand {
        text-align: center;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: #0d6efd;
    }
</style>
</head>

<body>

<div class="login-card">

    <div class="brand">
        <i class="bi bi-truck"></i> DRIV’N COOK
    </div>

    <h2>Connexion</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
                <input type="email" name="email" class="form-control" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>

        <div class="d-grid">
            <button class="btn btn-primary btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </button>
        </div>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

