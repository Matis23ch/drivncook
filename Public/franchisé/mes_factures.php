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
</head>

<body class="container mt-4">

<a href="dashboard.php" class="btn btn-outline-secondary mb-3">← Dashboard</a>

<h1 class="mb-4">Mes factures</h1>

<?php if (!$commandes): ?>
    <div class="alert alert-info">Aucune commande passée.</div>
<?php else: ?>

<table class="table table-bordered align-middle">
<thead class="table-light">
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
        <!-- Voir facture HTML -->
        <a href="facture.php?id=<?= $c['id'] ?>" 
           class="btn btn-primary btn-sm">
            Voir
        </a>

        <!-- Générer PDF -->
        <a href="facture_pdf.php?id=<?= $c['id'] ?>" 
           target="_blank"
           class="btn btn-outline-dark btn-sm">
            PDF
        </a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php endif; ?>

</body>
</html>

