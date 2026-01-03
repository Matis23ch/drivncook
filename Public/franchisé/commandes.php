<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/Commande.php';

session_start();
$franchise_id = $_SESSION['franchise_id'];

$produits = $pdo->query("SELECT * FROM produits")->fetchAll();

$selection = [];
$taux = null;
$valide = false;

if ($_POST) {
    foreach ($produits as $p) {
        if (isset($_POST['produit_' . $p['id']])) {
            $selection[] = $p;
        }
    }

    $taux = Commande::calculTauxDC($pdo, $selection);
    $valide = $taux >= 80;
}
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Passer une commande</h1>

<form method="POST">
<table class="table">
<tr><th>Produit</th><th>Origine</th><th>Sélection</th></tr>

<?php foreach ($produits as $p): ?>
<tr>
<td><?= $p['nom'] ?></td>
<td><?= $p['origine'] ?></td>
<td><input type="checkbox" name="produit_<?= $p['id'] ?>"></td>
</tr>
<?php endforeach; ?>
</table>

<button class="btn btn-primary">Calculer</button>
</form>

<?php if ($taux !== null): ?>
<div class="alert <?= $valide ? 'alert-success' : 'alert-danger' ?>">
Taux Driv’n Cook : <?= $taux ?> %
</div>
<?php endif; ?>

<a href="dashboard.php" class="btn btn-secondary">Retour</a>

</body>
</html>

