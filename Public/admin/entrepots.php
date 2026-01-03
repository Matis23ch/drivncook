<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/Produit.php';

$entrepots = [
    1 => 'Paris – Produits frais',
    2 => 'Créteil – Plats préparés',
    3 => 'Nanterre – Boissons',
    4 => 'Versailles – Stock mixte'
];

if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
}

if (isset($_POST['nom'], $_POST['entrepot_id'], $_POST['origine'])) {
    Produit::create($pdo, $_POST);
}


if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
}


$tauxDC = Produit::tauxDC($pdo);
?>
<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Gestion des entrepôts & produits</h1>


<?php foreach ($entrepots as $id => $nom): ?>
<h3><?= $nom ?></h3>

<form method="POST" class="row g-2 mb-2">
<input type="hidden" name="entrepot_id" value="<?= $id ?>">
<input name="nom" class="form-control col" placeholder="Nom du produit" required>
<input type="hidden" name="origine" value="DC">
<button class="btn btn-success col-2">Ajouter</button>
</form>



<table class="table table-sm">
<tr><th>Produit</th><th>Origine</th></tr>
<?php foreach (Produit::getByEntrepot($pdo, $id) as $p): ?>
<tr>
<td><?= $p['nom'] ?></td>
<td><?= $p['origine'] ?></td>
<td>
<form method="POST" style="display:inline">
<input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
<button class="btn btn-sm btn-danger">Supprimer</button>
</form>
</td>
</tr>

<?php endforeach; ?>
</table>

<?php endforeach; ?>

<a href="dashboard.php" class="btn btn-primary">Retour</a>

</body>
</html>



