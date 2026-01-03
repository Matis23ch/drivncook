<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

$stmt = $pdo->prepare("
    SELECT id, date_commande, taux_dc
    FROM commandes
    WHERE franchise_id = ?
    ORDER BY date_commande DESC
");
$stmt->execute([$franchise_id]);
$commandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Mes factures</h1>

<?php if (!$commandes): ?>
<div class="alert alert-info">Aucune commande passée.</div>
<?php else: ?>

<table class="table table-bordered">
<tr>
    <th>Date</th>
    <th>Taux DC</th>
    <th>Action</th>
</tr>

<?php foreach ($commandes as $c): ?>
<tr>
    <td><?= date('d/m/Y', strtotime($c['date_commande'])) ?></td>
    <td><?= $c['taux_dc'] ?> %</td>
    <td>
        <a href="facture.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">
            Voir la facture
        </a>
    </td>
</tr>
<?php endforeach; ?>

</table>
<?php endif; ?>

<a href="dashboard.php" class="btn btn-secondary mt-3">Retour</a>

</body>
</html>
