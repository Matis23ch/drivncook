<?php
session_start();
if ($_SESSION['role'] !== 'ADMIN') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/controllers/FranchiséController.php';

if ($_POST && isset($_POST['add'])) {
    FranchiséController::store($pdo, $_POST);
    header('Location: franchisés.php');
    exit;
}

if (isset($_GET['delete'])) {
    FranchiséController::destroy($pdo, (int)$_GET['delete']);
    header('Location: franchisés.php');
    exit;
}

$franchises = FranchiséController::index($pdo);
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Gestion des franchisés</h1>

<form method="POST" class="row g-2 mb-4">
<input name="nom" class="form-control col" placeholder="Nom" required>
<input name="email" type="email" class="form-control col" placeholder="Email" required>
<button name="add" class="btn btn-success col-2">Ajouter</button>
</form>

<table class="table table-bordered">
<tr>
<th>Nom</th>
<th>Email</th>
<th>Camion</th>
<th>Droit d’entrée</th>
<th>Redevance (4%)</th>
<th>Action</th>
</tr>

<?php foreach ($franchises as $f): ?>
<tr>
<td><?= htmlspecialchars($f['nom']) ?></td>
<td><?= htmlspecialchars($f['email']) ?></td>
<td><?= $f['camion_reference'] ?? 'Aucun' ?></td>
<td><?= $f['droit_paye'] ? '✅ Payé' : '❌ Non payé' ?></td>
<td><?= number_format(($f['ca_total'] ?? 0) * 0.04, 2) ?> €</td>
<td>
<a href="?delete=<?= $f['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
</td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>



