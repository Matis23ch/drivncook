<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

$camion = $pdo->prepare("
    SELECT id FROM camions WHERE franchise_id = ?
");
$camion->execute([$franchise_id]);
$camion = $camion->fetch();

if (!$camion) {
    header('Location: mon_camion.php');
    exit;
}

if (isset($_POST['type'])) {

    // 1️⃣ enregistrer la panne
    $stmt = $pdo->prepare("
        INSERT INTO pannes (camion_id, type)
        VALUES (?, ?)
    ");
    $stmt->execute([$camion['id'], $_POST['type']]);

    // 2️⃣ passer le camion en PANNE (TECHNIQUE)
    $pdo->prepare("
        UPDATE camions
        SET etat_technique = 'PANNE'
        WHERE id = ?
    ")->execute([$camion['id']]);

    header('Location: mon_camion.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Déclarer une panne</h1>

<form method="POST">
<select name="type" class="form-control mb-3" required>
<option value="">-- Type de panne --</option>
<option>Crevaison</option>
<option>Moteur</option>
<option>Freins</option>
<option>Électricité</option>
<option>Direction</option>
<option>Boîte de vitesses</option>
<option>Refroidissement</option>
<option>Carburant</option>
<option>Suspension</option>
<option>Autre</option>
</select>

<a href="dashboard.php" class="btn btn-outline-secondary mb-3">← Dashboard</a>

<button class="btn btn-danger">Déclarer la panne</button>
</form>

</body>
</html>

