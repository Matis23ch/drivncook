<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();

    // pour le projet : mot de passe en clair accepté
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
<title>Connexion</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Connexion DRIV'N COOK</h2>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Mot de passe</label>
<input type="password" name="password" class="form-control" required>
</div>

<button class="btn btn-primary">Se connecter</button>
</form>

</body>
</html>
