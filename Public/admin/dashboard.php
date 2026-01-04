<?php
require_once __DIR__ . '/../../config/database.php';

// 1️⃣ Somme des redevances de toutes les franchises actives
$redevances = $pdo->query("
    SELECT SUM(COALESCE(v.montant,0) * 0.04) AS total_redevances
    FROM franchises f
    LEFT JOIN ventes v ON v.franchise_id = f.id
    WHERE f.actif = 1
")->fetchColumn() ?? 0;

// 2️⃣ Somme des droits d'entrée payés
$droit_entree = $pdo->query("
    SELECT COUNT(*) 
    FROM franchises f
    WHERE f.actif = 1
      AND EXISTS (
          SELECT 1 FROM paiements p
          WHERE p.franchise_id = f.id
            AND p.type = 'DROIT_ENTREE'
      )
")->fetchColumn() ?? 0;

// 3️⃣ CA total
$ca = $redevances + ($droit_entree * 50000);

// Camions
$totalCamions = $pdo->query("SELECT COUNT(*) FROM camions")->fetchColumn();
$camionsHS = $pdo->query("SELECT COUNT(*) FROM camions WHERE etat='PANNE'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard DRIV’N COOK</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
html, body {
    height: 100%;
    margin: 0;
    font-family: 'Inter', sans-serif;
    background-color: #f0f2f5;
}

.container-full {
    min-height: 100vh;
    padding: 2rem;
}

h1 {
    font-weight: 600;
}

/* Stats */
.stats-row {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.stats-card {
    flex: 1;
    padding: 2rem;
    border-radius: 1.2rem;
    background-color: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    text-align: center;
}

.stats-card h5 {
    font-size: 1.1rem;
    color: #6c757d;
}

.stats-card p {
    font-size: 1.8rem;
    font-weight: 600;
}

/* Boutons bas */
.buttons-row {
    display: flex;
    gap: 1.5rem;
}

.btn-dashboard {
    flex: 1;
    padding: 1.3rem;
    font-size: 1.15rem;
    font-weight: 500;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.7rem;
}

.btn-dashboard i {
    font-size: 1.4rem;
}
</style>
</head>

<body>

<div class="container-full">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dashboard DRIV’N COOK</h1>
        <form action="/drivncook/public/logout.php" method="POST">
            <button class="btn btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </button>
        </form>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stats-card">
            <h5><i class="bi bi-currency-euro"></i> Chiffre d'affaires</h5>
            <p><?= number_format($ca, 2) ?> €</p>
        </div>
        <div class="stats-card">
            <h5><i class="bi bi-truck"></i> Camions</h5>
            <p><?= $totalCamions ?> <small>(<?= $camionsHS ?> en panne)</small></p>
        </div>
    </div>

    <!-- Boutons -->
    <div class="buttons-row">
        <a href="franchisés.php" class="btn btn-primary btn-dashboard">
            <i class="bi bi-people-fill"></i> Gérer les franchisés
        </a>

        <a href="camions.php" class="btn btn-primary btn-dashboard">
            <i class="bi bi-truck-flatbed"></i> Gérer les camions
        </a>

        <a href="entrepots.php" class="btn btn-primary btn-dashboard">
            <i class="bi bi-archive-fill"></i> Entrepôts
        </a>
    </div>

</div>

</body>
</html>


