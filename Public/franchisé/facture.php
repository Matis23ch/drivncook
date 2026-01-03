<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];
$commande_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* Vérification commande */
$stmt = $pdo->prepare("
    SELECT *
    FROM commandes
    WHERE id = ? AND franchise_id = ?
");
$stmt->execute([$commande_id, $franchise_id]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    die('Commande introuvable');
}

/* Lignes de commande */
$stmt = $pdo->prepare("
    SELECT 
        cl.quantite,
        cl.prix,
        cl.produit_id,
        p.nom
    FROM commande_lignes cl
    LEFT JOIN produits p ON p.id = cl.produit_id
    WHERE cl.commande_id = ?
");
$stmt->execute([$commande_id]);
$lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h1>Facture</h1>

<p>
<strong>Date :</strong> <?= date('d/m/Y', strtotime($commande['date_commande'])) ?><br>
<strong>Taux Driv’n Cook :</strong> <?= $commande['taux_dc'] ?> %
</p>

<table class="table table-bordered mt-3">
<thead>
<tr>
    <th>Produit</th>
    <th>Type</th>
    <th>Quantité</th>
    <th>Prix unitaire</th>
    <th>Total</th>
</tr>
</thead>
<tbody>

<?php if (empty($lignes)): ?>
<tr>
    <td colspan="5" class="text-center">Aucune ligne</td>
</tr>
<?php endif; ?>

<?php foreach ($lignes as $l): 
    $ligneTotal = $l['quantite'] * $l['prix'];
    $total += $ligneTotal;
?>
<tr>
    <td><?= htmlspecialchars($l['nom'] ?? 'Produit libre hors DC') ?></td>
    <td><?= $l['produit_id'] ? 'Driv’n Cook' : 'Produit libre hors DC' ?></td>
    <td><?= $l['quantite'] ?></td>
    <td><?= number_format($l['prix'], 2) ?> €</td>
    <td><?= number_format($ligneTotal, 2) ?> €</td>
</tr>
<?php endforeach; ?>

<tr>
    <th colspan="4" class="text-end">TOTAL</th>
    <th><?= number_format($total, 2) ?> €</th>
</tr>

</tbody>
</table>

<a href="mes_factures.php" class="btn btn-secondary mt-3">Retour aux factures</a>

</body>
</html>

