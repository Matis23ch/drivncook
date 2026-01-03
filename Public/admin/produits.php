<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/controllers/ProduitController.php';
require_once __DIR__ . '/../../app/controllers/EntrepotController.php';

if ($_POST) {
    ProduitController::store($pdo, $_POST);
}

$produits = ProduitController::index($pdo);
$entrepots = EntrepotController::index($pdo);
$tauxDC = ProduitController::tauxDC($pdo);
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Gestion des produits</h1>

<div class="alert <?= $tauxDC < 80 ? 'alert-danger' : 'alert-success' ?>">
Taux produits Driv’n Cook : <strong><?= $tauxDC ?> %</strong>
</div>

<form method="POST" class="row g-2 mb-4">
<input name="nom" class="form-control col" placeholder="Nom produit" required>

<select name="entrepot_id" class="form-control col" required>
<?php foreach ($entrepots as $e): ?>
<option value="<?= $e['id'] ?>"><?= $e['nom'] ?></option>
<?php endforeach; ?>
</select>

<select name="origine" class="form-control col" required>
<option value="DC">Driv’n Cook</option>
<option value="LIBRE">Libre</option>
</select>

<button class="btn btn-success col-2">Ajouter</button>
</form>

<table class="table table-bordered">
<tr>
<th>Produit</th>
<th>Entrepôt</th>
<th>Origine</th>
</tr>
<?php foreach ($produits as $p): ?>
<tr>
<td><?= $p['nom'] ?></td>
<td><?= $p['entrepot'] ?></td>
<td><?= $p['origine'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
