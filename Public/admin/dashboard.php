<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/Produit.php';

$ca = $pdo->query("SELECT SUM(montant) FROM ventes")->fetchColumn() ?? 0;

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
