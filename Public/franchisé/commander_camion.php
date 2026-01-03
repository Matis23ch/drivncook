<?php

require_once __DIR__ . '/../../config/database.php';

session_start();
$franchise_id = $_SESSION['franchise_id'];

if ($_POST) {
    $stmt = $pdo->prepare("
        UPDATE camions
        SET franchise_id = ?, etat = 'OK'
        WHERE id = ? AND franchise_id IS NULL
    ");
    $stmt->execute([$franchise_id, $_POST['camion_id']]);
    header('Location: mon_camion.php');
    exit;
}

$camions = $pdo->query("
    SELECT * FROM camions WHERE franchise_id IS NULL
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<body class="container">

<h1>Choisir un camion</h1>

<form method="POST">
<select name="camion_id" class="form-control">
<?php foreach ($camions as $c): ?>
<option value="<?= $c['id'] ?>"><?= $c['reference'] ?></option>
<?php endforeach; ?>
</select>

<button class="btn btn-success mt-2">Valider</button>
</form>

</body>
</html>
