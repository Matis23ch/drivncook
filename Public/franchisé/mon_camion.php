<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

/* Récupération du camion attribué */
$camion = $pdo->prepare("
    SELECT *
    FROM camions
    WHERE franchise_id = ?
");
$camion->execute([$franchise_id]);
$camion = $camion->fetch();
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Mon camion</h1>

<?php if (!$camion): ?>

<div class="alert alert-warning">
Aucun camion attribué. Veuillez en commander un.
</div>

<a href="choisir_camion.php" class="btn btn-primary">Choisir un camion</a>

<?php else: ?>

<table class="table">
<tr><th>Référence</th><td><?= htmlspecialchars($camion['reference']) ?></td></tr>
<tr><th>Localisation</th><td><?= htmlspecialchars($camion['localisation']) ?></td></tr>
<tr><th>État</th>
<td>
<?= $camion['etat_technique'] === 'PANNE' ? '🚨 Panne déclarée' : '✅ Opérationnel' ?>
</td>
</tr>
</table>

<a href="signaler_panne.php" class="btn btn-danger">Déclarer une panne</a>
<a href="dashboard.php" class="btn btn-secondary mt-3">Retour à l’accueil</a>

<?php endif; ?>

</body>
</html>




