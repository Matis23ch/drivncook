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
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Déclarer mon chiffre d’affaires (A faire chaque mois)</h1>

<form method="POST" class="mb-4">
<input type="number" step="0.01" name="montant" class="form-control mb-2" placeholder="Montant €" required>
<button class="btn btn-success">Déclarer</button>
</form>

<a href="dashboard.php" class="btn btn-secondary">Retour</a>

</body>
</html>
