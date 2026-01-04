<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'ADMIN') {
    header('Location: ../login.php');
    exit;
}

/* =======================
   CRÉATION CAMION
======================= */
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("
        INSERT INTO camions (reference, localisation, etat, etat_technique)
        VALUES (?, ?, 'DISPONIBLE', 'OPERATIONNEL')
    ");
    $stmt->execute([
        $_POST['reference'],
        $_POST['localisation']
    ]);
}

/* =======================
   LIBÉRER CAMION
======================= */
if (isset($_GET['liberer'])) {
    $stmt = $pdo->prepare("
        UPDATE camions
        SET franchise_id = NULL,
            etat = 'DISPONIBLE'
        WHERE id = ?
    ");
    $stmt->execute([$_GET['liberer']]);
}

/* =======================
   RÉPARER CAMION
======================= */
if (isset($_GET['reparer'])) {
    $stmt = $pdo->prepare("
        UPDATE camions
        SET etat_technique = 'OPERATIONNEL'
        WHERE id = ?
    ");
    $stmt->execute([$_GET['reparer']]);
}

/* =======================
   SUPPRIMER CAMION
======================= */
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("
        DELETE FROM camions WHERE id = ?
    ");
    $stmt->execute([$_GET['delete']]);
}

/* =======================
   LISTE CAMIONS
======================= */
$camions = $pdo->query("
    SELECT c.*,
           u.email AS franchise_email,
           p.type AS type_panne
    FROM camions c
    LEFT JOIN users u ON u.franchise_id = c.franchise_id
    LEFT JOIN pannes p ON p.camion_id = c.id
        AND p.date_panne = (
            SELECT MAX(date_panne)
            FROM pannes
            WHERE camion_id = c.id
        )
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des camions</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background-color: #f0f2f5;
        font-family: 'Inter', sans-serif;
    }

    .page-container {
        max-width: 1300px;
        margin: auto;
        padding: 2rem;
    }

    .card {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }

    h1 {
        font-weight: 600;
    }

    .btn-rounded {
        border-radius: .75rem;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>
</head>

<body>

<div class="page-container">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-truck"></i> Gestion des camions</h1>
        <a href="dashboard.php" class="btn btn-outline-primary btn-rounded">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>

    <!-- CRÉATION CAMION -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Créer un camion</h5>

            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <input name="reference" class="form-control" placeholder="Référence du camion" required>
                </div>

                <div class="col-md-4">
                    <input name="localisation" class="form-control" placeholder="Localisation" required>
                </div>

                <div class="col-md-2 d-grid">
                    <button name="add" class="btn btn-success btn-rounded">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLE CAMIONS -->
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Liste des camions</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Localisation</th>
                            <th>Disponibilité</th>
                            <th>État technique</th>
                            <th>Franchisé</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($camions as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['reference']) ?></td>
                            <td><?= htmlspecialchars($c['localisation']) ?></td>

                            <td>
                                <?= $c['etat'] === 'ATTRIBUE'
                                    ? '<span class="badge bg-warning text-dark">Attribué</span>'
                                    : '<span class="badge bg-success">Disponible</span>' ?>
                            </td>

                            <td>
                                <?php if ($c['etat_technique'] === 'PANNE'): ?>
                                    <span class="badge bg-danger">
                                        Panne (<?= htmlspecialchars($c['type_panne']) ?>)
                                    </span>
                                    <a href="?reparer=<?= $c['id'] ?>"
                                       class="btn btn-success btn-sm btn-rounded ms-2">
                                        <i class="bi bi-wrench"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-success">Opérationnel</span>
                                <?php endif; ?>
                            </td>

                            <td><?= $c['franchise_email'] ?? '-' ?></td>

                            <td class="text-center">
                                <?php if ($c['franchise_id']): ?>
                                    <a href="?liberer=<?= $c['id'] ?>"
                                       class="btn btn-warning btn-sm btn-rounded me-1">
                                        <i class="bi bi-unlock"></i>
                                    </a>
                                <?php endif; ?>

                                <a href="?delete=<?= $c['id'] ?>"
                                   class="btn btn-danger btn-sm btn-rounded"
                                   onclick="return confirm('Supprimer ce camion ?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





