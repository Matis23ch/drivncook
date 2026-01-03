<?php
require '../../config/database.php';

session_start();
$franchise_id = $_SESSION['franchise_id'];

if ($_POST) {
    $stmt = $pdo->prepare("
        INSERT INTO paiements (franchise_id, type, montant, date_paiement)
        VALUES (?, 'DROIT_ENTREE', 50000, NOW())
    ");
    $stmt->execute([1]);
}
?>
<!DOCTYPE html>
<html>
<body class="container">
<h2>Droit d’entrée</h2>

<form method="POST">
<button class="btn btn-success">
Payer 50 000 €
</button>
</form>

<p>Une fois payé, le camion est attribué.</p>
</body>
</html>
