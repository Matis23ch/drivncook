<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/Franchisé.php';
require_once __DIR__ . '/../../app/models/Camion.php';
require_once __DIR__ . '/../../app/models/Commande.php';
require_once __DIR__ . '/../../app/models/Paiement.php';

// Récupère l'id passé dans l'URL
$franchise_id = $_GET['id'] ?? null;
if (!$franchise_id) {
    header('Location: franchisés.php');
    exit;
}

// Infos franchise
$franchise = Franchisé::getById($pdo, $franchise_id);

// Camion attribué
$camion = Camion::getByFranchise($pdo, $franchise_id);

// Commandes / % DC
$commandes = Commande::getByFranchise($pdo, $franchise_id);

// Paiements
$droit_paye = Paiement::droitEntreePaye($pdo, $franchise_id);
$redevance_total = Paiement::totalRedevance($pdo, $franchise_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Détails Franchise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Franchise : <?= $franchise['nom'] ?></h1>
<p>Email : <?= $franchise['email'] ?></p>
<p>Droit d'entrée : <?= $droit_paye ? '✅ Payé' : '❌ Non payé' ?></p>
<p>Redevance totale : <?= $redevance_total ?> €</p>

<h3>Camion attribué</h3>
<?php if ($camion): ?>
<table class="table">
<tr><th>Référence</th><td><?= $camion['reference'] ?></td></tr>
<tr><th>État</th><td><?= $camion['etat'] ?></td></tr>
<tr><th>Dernière panne</th><td><?= $camion['derniere_panne'] ?? 'Aucune' ?></td></tr>
</table>
<?php else: ?>
<p>Aucun camion attribué</p>
<?php endif; ?>

<h3>Commandes et approvisionnement</h3>
<?php if ($commandes): ?>
<table class="table table-bordered">
<tr>
<th>Produit</th>
<th>Quantité</th>
<th>Origine</th>
<th>% DC</th>
</tr>
<?php foreach ($commandes as $c): ?>
<tr>
<td><?= $c['produit_nom'] ?></td>
<td><?= $c['quantite'] ?></td>
<td><?= $c['origine'] ?></td>
<td><?= $c['origine'] === 'DC' ? '100%' : '0%' ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>Aucune commande enregistrée</p>
<?php endif; ?>

<a href="franchisés.php" class="btn btn-secondary mt-3">Retour</a>
</body>
</html>
