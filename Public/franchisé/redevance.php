<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/Paiement.php';

session_start();
$franchise_id = $_SESSION['franchise_id'];


// calcul CA
$ca = $pdo->query("
    SELECT SUM(montant) FROM ventes WHERE franchise_id = $franchise_id
")->fetchColumn() ?? 0;

$redevance = round($ca * 0.04, 2);

if ($_POST) {
    Paiement::payerRedevance($pdo, $franchise_id, $redevance);
    $message = "Redevance payée avec succès";
}
?>
<!DOCTYPE html>
<html>
<body class="container">

<h1>Redevance</h1>

<p>Chiffre d’affaires : <?= $ca ?> €</p>
<p>Redevance (4 %) : <strong><?= $redevance ?> €</strong></p>

<form method="POST">
<button class="btn btn-success">Payer la redevance</button>
</form>

<?php if (isset($message)): ?>
<div class="alert alert-success mt-3"><?= $message ?></div>
<?php endif; ?>

<a href="dashboard.php" class="btn btn-primary mt-3">Retour</a>

</body>
</html>

