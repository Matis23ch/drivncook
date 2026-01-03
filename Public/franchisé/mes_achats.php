<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$franchise_id = $_SESSION['franchise_id'];

$commandes = $pdo->prepare("
    SELECT * FROM commandes
    WHERE franchise_id = ?
    ORDER BY date_commande DESC
");
$commandes->execute([$franchise_id]);
$commandes = $commandes->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Mes achats</h1>

<?php foreach ($commandes as $c): ?>
<div class="card mb-3 p-3">
<h5>Commande du <?= $c['date_commande'] ?> – <?= $c['taux_dc'] ?> % DC</h5>

<ul>
<?php
$lignes = $pdo->prepare("
    SELECT l.*, p.nom
    FROM commande_lignes l
    LEFT JOIN produits p ON p.id = l.produit_id
    WHERE commande_id = ?
");
$lignes->execute([$c['id']]);

foreach ($lignes as $l):
?>
<li>
<?= $l['type'] === 'DC' ? $l['nom'] : 'Produit libre' ?>
 – <?= $l['quantite'] ?> × <?= number_format($l['prix'], 2) ?> €
</li>
<?php endforeach; ?>
</ul>
</div>
<?php endforeach; ?>

<a href="dashboard.php" class="btn btn-secondary">Retour</a>

</body>
</html>
