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
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mes factures</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    font-family: 'Inter', sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    padding: 2rem;
}

h1 {
    font-weight: 600;
    margin-bottom: 2rem;
}

.table {
    background-color: #fff;
    border-radius: 0.8rem;
    overflow: hidden;
}

.table thead {
    background-color: #f8f9fa;
}

.btn-modern {
    border-radius: 0.8rem;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.bottom-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}
</style>
</head>

<body>

<h1>Mes factures</h1>

<?php if (!$commandes): ?>
    <div class="alert alert-info">Aucune commande passée.</div>
<?php else: ?>

<table class="table table-bordered align-middle">
<thead>
<tr>
    <th>Date</th>
    <th>Taux DC</th>
    <th style="width: 220px;">Actions</th>
</tr>
</thead>

<tbody>
<?php foreach ($commandes as $c): ?>
<tr>
    <td><?= date('d/m/Y', strtotime($c['date_commande'])) ?></td>
    <td><?= htmlspecialchars($c['taux_dc']) ?> %</td>
    <td class="d-flex gap-2">
        <a href="facture.php?id=<?= $c['id'] ?>" 
           class="btn btn-primary btn-modern">
            Voir
        </a>
        <a href="facture_pdf.php?id=<?= $c['id'] ?>" 
           target="_blank"
           class="btn btn-outline-dark btn-modern">
            PDF
        </a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php endif; ?>

<div class="bottom-buttons" style="max-width:400px;">
    <a href="dashboard.php" class="btn btn-secondary btn-modern">
        ← Retour au dashboard
    </a>
</div>

</body>
</html>


