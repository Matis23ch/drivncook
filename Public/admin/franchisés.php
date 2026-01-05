<?php
session_start();
if ($_SESSION['role'] !== 'ADMIN') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/controllers/FranchiséController.php';

if ($_POST && isset($_POST['add'])) {
    FranchiséController::store($pdo, $_POST);
    header('Location: franchisés.php');
    exit;
}

if (isset($_GET['delete'])) {
    FranchiséController::destroy($pdo, (int)$_GET['delete']);
    header('Location: franchisés.php');
    exit;
}

$franchises = FranchiséController::index($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des franchisés</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background-color: #f0f2f5;
        font-family: 'Inter', sans-serif;
    }

    .page-container {
        max-width: 1200px;
        margin: auto;
        padding: 2rem;
    }

    .card {
        border-radius: 1rem;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        border: none;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    h1 {
        font-weight: 600;
    }

    .btn-rounded {
        border-radius: .75rem;
    }
</style>
</head>

<body>

<div class="page-container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-people-fill"></i> Gestion des franchisés</h1>
        <a href="dashboard.php" class="btn btn-outline-primary btn-rounded">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Ajouter un franchisé</h5>

            <form method="POST" class="row g-3">
                <div class="col-md-5">
                    <input name="nom" class="form-control" placeholder="Nom du franchisé" required>
                </div>

                <div class="col-md-5">
                    <input name="email" type="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="col-md-2 d-grid">
                    <button name="add" class="btn btn-success btn-rounded">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Liste des franchisés</h5>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Camion</th>
                            <th>Droit d’entrée</th>
                            <th>Redevance (4%)</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($franchises as $f): ?>
                        <tr>
                            <td><?= htmlspecialchars($f['nom']) ?></td>
                            <td><?= htmlspecialchars($f['email']) ?></td>
                            <td><?= $f['camion_reference'] ?? 'Aucun' ?></td>
                            <td>
                                <?= $f['droit_paye']
                                    ? '<span class="badge bg-success">Payé</span>'
                                    : '<span class="badge bg-danger">Non payé</span>' ?>
                            </td>
                            <td><?= number_format(($f['ca_total'] ?? 0) * 0.04, 2) ?> €</td>
                            <td class="text-center">
                                <a href="?delete=<?= $f['id'] ?>"
                                   class="btn btn-danger btn-sm btn-rounded"
                                   onclick="return confirm('Supprimer ce franchisé ?')">
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




