<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

// Vérifier si droit d'entrée payé
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

// Paiement droit d'entrée
if (isset($_POST['payer'])) {
    $stmt = $pdo->prepare("
        INSERT INTO paiements (franchise_id, type, montant, date_paiement)
        VALUES (?, 'DROIT_ENTREE', 50000, NOW())
    ");
    $stmt->execute([$franchise_id]);

    header('Location: mon_camion.php');
    exit;
}

// Choisir un camion
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

// Camions disponibles
$camions_dispo = $pdo->query("SELECT * FROM camions WHERE etat='DISPONIBLE'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mon Camion – Dashboard Franchisé</title>
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
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .alert-modern {
        padding: 1rem 1.25rem;
        border-radius: 0.8rem;
        font-size: 1rem;
    }

    .btn-modern {
        border-radius: 0.8rem;
        padding: 0.7rem 1.5rem;
        font-size: 1rem;
    }

    .table-modern {
        background-color: #ffffff;
        border-radius: 0.8rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .table-modern th, .table-modern td {
        vertical-align: middle;
    }

    select.form-control {
        border-radius: 0.8rem;
    }

    .actions {
        margin-top: 1rem;
    }

    /* Nouvel alignement des deux boutons du bas */
    .bottom-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .bottom-buttons .btn-modern {
        flex: 1; /* prennent la même largeur */
    }
</style>
</head>
<body>

<h1>Mon Camion</h1>

<?php if (!$droit_paye): ?>
    <div class="alert alert-warning alert-modern">
        ⚠️ Vous devez payer le droit d'entrée de <strong>50 000 €</strong> pour choisir un camion.
    </div>
    <form method="POST" class="actions">
        <button name="payer" class="btn btn-success btn-modern">
            Payer 50 000 €
        </button>
    </form>

<?php elseif (!$camion): ?>
    <?php if (empty($camions_dispo)): ?>
        <div class="alert alert-info alert-modern">
            ℹ️ Aucun camion disponible pour le moment.
        </div>
    <?php else: ?>
        <form method="POST" class="actions">
            <select name="camion_id" class="form-control mb-3" required>
                <?php foreach ($camions_dispo as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['reference']) ?> – <?= htmlspecialchars($c['localisation']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-success btn-modern">Valider le choix</button>
        </form>
    <?php endif; ?>

<?php else: ?>
    <div class="table-responsive table-modern">
        <table class="table table-bordered mb-3">
            <tr><th>Référence</th><td><?= htmlspecialchars($camion['reference']) ?></td></tr>
            <tr><th>Localisation</th><td><?= htmlspecialchars($camion['localisation']) ?></td></tr>
            <tr><th>État</th>
                <td>
                    <?= $camion['etat_technique'] === 'PANNE' ? '🚨 Panne déclarée' : '✅ Opérationnel' ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="bottom-buttons">
        <a href="signaler_panne.php" class="btn btn-danger btn-modern">Déclarer une panne</a>
        <a href="dashboard.php" class="btn btn-secondary btn-modern">Retour à l’accueil</a>
    </div>
<?php endif; ?>

</body>
</html>






