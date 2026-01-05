<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];
$commande_id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM commandes
    WHERE id = ? AND franchise_id = ?
");
$stmt->execute([$commande_id, $franchise_id]);
$commande = $stmt->fetch();

if (!$commande) die("Commande introuvable");

$stmt = $pdo->prepare("
    SELECT cl.*, p.nom
    FROM commande_lignes cl
    LEFT JOIN produits p ON p.id = cl.produit_id
    WHERE cl.commande_id = ?
");
$stmt->execute([$commande_id]);
$lignes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<a href="dashboard.php" class="btn btn-outline-secondary mb-3">← Dashboard</a>

<h1>Facture</h1>

<p>
<strong>Date :</strong> <?= date('d/m/Y', strtotime($commande['date_commande'])) ?><br>
<strong>Taux DC :</strong> <?= $commande['taux_dc'] ?> %
</p>

<table class="table table-bordered">
<thead>
<tr>
    <th>Produit</th>
    <th>Type</th>
    <th>Quantité</th>
    <th>Montant</th>
</tr>
</thead>
<tbody>

<?php 
$totalDC = 0;
foreach ($lignes as $l):
    // On n'inclut que les produits DC
    if ($l['type'] !== 'DC') continue;

    $ligneTotal = $l['quantite'] * $l['prix'];
    $totalDC += $ligneTotal;
?>
<tr>
    <td><?= htmlspecialchars($l['nom']) ?></td>
    <td><?= $l['type'] ?></td>
    <td><?= $l['quantite'] ?></td>
    <td><?= number_format($ligneTotal, 2) ?> €</td>
</tr>
<?php endforeach; ?>

<tr>
    <th colspan="3" class="text-end">TOTAL Produits DC</th>
    <th><?= number_format($totalDC, 2) ?> €</th>
</tr>
</tbody>
</table>

<a href="mes_factures.php" class="btn btn-secondary">Retour aux factures</a>

</body>
</html>




