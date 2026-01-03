<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Franchisé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Dashboard Franchisé</h1>

    <!-- Déconnexion -->
    <form action="../logout.php" method="POST">
        <button type="submit" class="btn btn-danger">
            Déconnexion
        </button>
    </form>
</div>

<p class="text-muted">Bienvenue sur votre espace franchisé.</p>

<div class="list-group mt-4">
    <a href="mon_camion.php" class="list-group-item list-group-item-action">
        🚚 Mon camion
    </a>

    <a href="commandes.php" class="list-group-item list-group-item-action">
        📦 Mes commandes
    </a>

    <a href="ventes.php" class="list-group-item list-group-item-action">
        💰 Mes ventes
    </a>

    <a href="mes_factures.php" class="list-group-item list-group-item-action">
        🧾 Mes factures
    </a>
</div>

</body>
</html>


