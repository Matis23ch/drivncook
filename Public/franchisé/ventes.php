<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

if (isset($_POST['montant'])) {
    $stmt = $pdo->prepare("
        INSERT INTO ventes (franchise_id, montant, date_vente)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([$franchise_id, $_POST['montant']]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Déclarer mes ventes</title>
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

form {
    max-width: 400px;
    margin-bottom: 2rem;
}

input.form-control {
    border-radius: 0.8rem;
    padding: 0.6rem;
}

.btn-modern {
    border-radius: 0.8rem;
    padding: 0.6rem 1.5rem;
    font-size: 1rem;
    width: 100%;
    margin-top: 0.5rem;
}

.bottom-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}
</style>
</head>

<body>

<h1>Déclarer mon chiffre d’affaires</h1>
<p class="text-muted">À faire chaque mois</p>

<form method="POST">
    <input type="number" step="0.01" name="montant" class="form-control mb-2" placeholder="Montant €" required>
    <button class="btn btn-success btn-modern">Déclarer</button>
</form>

<div class="bottom-buttons" style="max-width:400px;">
    <a href="dashboard.php" class="btn btn-secondary btn-modern">
        ← Retour au dashboard
    </a>
</div>

</body>
</html>

