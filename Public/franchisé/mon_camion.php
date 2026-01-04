<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

// Vérifier si droit d'entrée payé (on ne vérifie QUE type et franchise_id)
$droit_paye = $pdo->prepare("
    SELECT COUNT(*) 
    FROM paiements 
    WHERE franchise_id = ? 
      AND type = 'DROIT_ENTREE'
");
$droit_paye->execute([$franchise_id]);
$droit_paye = $droit_paye->fetchColumn() > 0;

// Récupérer le camion actuel
$camion = $pdo->prepare("SELECT * FROM camions WHERE franchise_id = ?");
$camion->execute([$franchise_id]);
$camion = $camion->fetch();

// Si le formulaire de paiement est envoyé
if (isset($_POST['payer'])) {
    $stmt = $pdo->prepare("
        INSERT INTO paiements (franchise_id, type, montant, date_paiement)
        VALUES (?, 'DROIT_ENTREE', 50000, NOW())
    ");
    $stmt->execute([$franchise_id]);

    header('Location: mon_camion.php');
    exit;
}

// Si le formulaire de choix de camion est envoyé
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

// Camions disponibles pour choisir
$camions_dispo = $pdo->query("SELECT * FROM camions WHERE etat='DISPONIBLE'")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Mon camion</h1>

<?php if (!$droit_paye): ?>
    <!-- Formulaire paiement droit d'entrée -->
    <div class="alert alert-warning">
        Vous devez payer le droit d'entrée de 50 000 € pour choisir un camion.
    </div>
    <form method="POST">
        <button name="payer" class="btn btn-success">Payer 50 000 €</button>
    </form>

<?php elseif (!$camion): ?>
    <!-- Choisir un camion -->
    <?php if (empty($camions_dispo)): ?>
        <div class="alert alert-info">Aucun camion disponible pour le moment.</div>
    <?php else: ?>
        <form method="POST">
            <select name="camion_id" class="form-control mb-3" required>
                <?php foreach ($camions_dispo as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['reference']) ?> – <?= htmlspecialchars($c['localisation']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-success">Valider</button>
        </form>
    <?php endif; ?>

<?php else: ?>
    <!-- Affichage camion attribué -->
    <table class="table">
        <tr><th>Référence</th><td><?= htmlspecialchars($camion['reference']) ?></td></tr>
        <tr><th>Localisation</th><td><?= htmlspecialchars($camion['localisation']) ?></td></tr>
        <tr><th>État</th>
            <td><?= $camion['etat_technique'] === 'PANNE' ? '🚨 Panne déclarée' : '✅ Opérationnel' ?></td>
        </tr>
    </table>
    <a href="signaler_panne.php" class="btn btn-danger">Déclarer une panne</a>
<?php endif; ?>

<a href="dashboard.php" class="btn btn-secondary mt-3">Retour à l’accueil</a>

</body>
</html>





