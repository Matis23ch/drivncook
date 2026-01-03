<?php
require_once __DIR__ . '/../../config/database.php';

// 1️⃣ Somme des redevances de toutes les franchises actives
$redevances = $pdo->query("
    SELECT SUM(COALESCE(v.montant,0) * 0.04) AS total_redevances
    FROM franchises f
    LEFT JOIN ventes v ON v.franchise_id = f.id
    WHERE f.actif = 1
")->fetchColumn() ?? 0;

// 2️⃣ Somme des droits d'entrée payés (50 000€ pour chaque franchise payée)
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

// 3️⃣ Calcul du CA total affiché dans le dashboard
$ca = $redevances + ($droit_entree * 50000);

// Camions
$totalCamions = $pdo->query("SELECT COUNT(*) FROM camions")->fetchColumn();
$camionsHS = $pdo->query("SELECT COUNT(*) FROM camions WHERE etat='PANNE'")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Dashboard DRIV’N COOK</h1>

<div class="row">

<div class="col-md-3">
<div class="card p-3">
<h5>Chiffre d'affaires</h5>
<p><?= number_format($ca, 2) ?> €</p>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h5>Camions</h5>
<p><?= $totalCamions ?> (<?= $camionsHS ?> en panne)</p>
</div>
</div>

</div>

<a href="franchisés.php" class="btn btn-primary mt-3">Gérer les franchisés</a>
<a href="camions.php" class="btn btn-primary mt-3">Gérer les camions</a>
<a href="entrepots.php" class="btn btn-primary mt-3">Entrepôts</a>

</body>
</html>

