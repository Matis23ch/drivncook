<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$franchise_id = $_SESSION['franchise_id'];

/* Vérifier paiement */
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM paiements 
    WHERE franchise_id = ? 
    AND type = 'DROIT_ENTREE'
");
$stmt->execute([$franchise_id]);

if ($stmt->fetchColumn() == 0) {
    header('Location: mon_camion.php');
    exit;
}


if (isset($_POST['camion_id'])) {
    $stmt = $pdo->prepare("
        UPDATE camions
        SET franchise_id = ?, etat = 'ATTRIBUE'
        WHERE id = ? AND etat = 'DISPONIBLE'
    ");
    $stmt->execute([$franchise_id, $_POST['camion_id']]);

    header('Location: mon_camion.php');
    exit;
}

$camions = $pdo->query("
    SELECT * FROM camions
    WHERE etat = 'DISPONIBLE'
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<body class="container">

<h1>Choisir un camion</h1>

<form method="POST">
<select name="camion_id" class="form-control mb-3" required>
<?php foreach ($camions as $c): ?>
<option value="<?= $c['id'] ?>">
<?= $c['reference'] ?> – <?= $c['localisation'] ?>
</option>
<?php endforeach; ?>
</select>

<button class="btn btn-success">Valider</button>
</form>

</body>
</html>
