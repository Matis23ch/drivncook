<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

$camion = $pdo->prepare("SELECT id FROM camions WHERE franchise_id = ?");
$camion->execute([$franchise_id]);
$camion = $camion->fetch();

if (!$camion) {
    header('Location: mon_camion.php');
    exit;
}

if (isset($_POST['type'])) {
    // 1️⃣ Enregistrer la panne
    $stmt = $pdo->prepare("INSERT INTO pannes (camion_id, type) VALUES (?, ?)");
    $stmt->execute([$camion['id'], $_POST['type']]);

    // 2️⃣ Passer le camion en PANNE (technique)
    $pdo->prepare("UPDATE camions SET etat_technique = 'PANNE' WHERE id = ?")
        ->execute([$camion['id']]);

    header('Location: mon_camion.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Déclarer une panne</title>
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
    margin-bottom: 1rem;
}

select.form-control {
    border-radius: 0.8rem;
}

.btn-modern {
    border-radius: 0.8rem;
    padding: 0.7rem 1.5rem;
    font-size: 1rem;
}

.bottom-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.bottom-buttons .btn-modern {
    flex: 1;
}
</style>
</head>

<body>

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

    <div class="bottom-buttons">
        <a href="dashboard.php" class="btn btn-secondary btn-modern">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
        <button type="submit" class="btn btn-danger btn-modern">
            Déclarer la panne
        </button>
    </div>
</form>

</body>
</html>


