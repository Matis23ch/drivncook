<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'ADMIN') {
    header('Location: ../login.php');
    exit;
}

/* =======================
   CRÉATION CAMION
======================= */
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("
        INSERT INTO camions (reference, localisation, etat, etat_technique)
        VALUES (?, ?, 'DISPONIBLE', 'OPERATIONNEL')
    ");
    $stmt->execute([
        $_POST['reference'],
        $_POST['localisation']
    ]);
}

/* =======================
   LIBÉRER CAMION
   ➜ NE RÉPARE PAS
======================= */
if (isset($_GET['liberer'])) {
    $stmt = $pdo->prepare("
        UPDATE camions
        SET franchise_id = NULL,
            etat = 'DISPONIBLE'
        WHERE id = ?
    ");
    $stmt->execute([$_GET['liberer']]);
}

/* =======================
   RÉPARER CAMION (ADMIN)
======================= */
if (isset($_GET['reparer'])) {
    $stmt = $pdo->prepare("
        UPDATE camions
        SET etat_technique = 'OPERATIONNEL'
        WHERE id = ?
    ");
    $stmt->execute([$_GET['reparer']]);
}

/* =======================
   SUPPRESSION CAMION
======================= */
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("
        DELETE FROM camions WHERE id = ?
    ");
    $stmt->execute([$_GET['delete']]);
}

/* =======================
   LISTE CAMIONS + PANNE
======================= */
$camions = $pdo->query("
    SELECT c.*,
           u.email AS franchise_email,
           p.type AS type_panne
    FROM camions c
    LEFT JOIN users u ON u.franchise_id = c.franchise_id
    LEFT JOIN pannes p ON p.camion_id = c.id
        AND p.date_panne = (
            SELECT MAX(date_panne)
            FROM pannes
            WHERE camion_id = c.id
        )
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h1>Gestion des camions</h1>

<h4>Créer un camion</h4>
<form method="POST" class="row g-2 mb-4">
    <input name="reference" class="form-control col" placeholder="Référence" required>
    <input name="localisation" class="form-control col" placeholder="Localisation" required>
    <button name="add" class="btn btn-success col-2">Ajouter</button>
</form>

<table class="table table-bordered">
<tr>
<th>Référence</th>
<th>Localisation</th>
<th>Disponibilité</th>
<th>État technique</th>
<th>Franchisé</th>
<th>Actions</th>
</tr>

<?php foreach ($camions as $c): ?>
<tr>

<td><?= htmlspecialchars($c['reference']) ?></td>
<td><?= htmlspecialchars($c['localisation']) ?></td>

<td>
<?= $c['etat'] === 'ATTRIBUE' ? '🚚 Attribué' : '🟢 Disponible' ?>
</td>

<td>
<?php if ($c['etat_technique'] === 'PANNE'): ?>
🚨 Panne (<?= htmlspecialchars($c['type_panne']) ?>)
<a href="?reparer=<?= $c['id'] ?>" class="btn btn-success btn-sm ms-2">
Réparer
</a>
<?php else: ?>
✅ Opérationnel
<?php endif; ?>
</td>

<td><?= $c['franchise_email'] ?? '-' ?></td>

<td>
<?php if ($c['franchise_id']): ?>
<a href="?liberer=<?= $c['id'] ?>" class="btn btn-warning btn-sm">
Libérer
</a>
<?php endif; ?>
<a href="?delete=<?= $c['id'] ?>" class="btn btn-danger btn-sm">
Supprimer
</a>
</td>

</tr>
<?php endforeach; ?>
</table>

<a href="dashboard.php" class="btn btn-primary mt-3">Retour au Dashboard</a>

</body>
</html>




