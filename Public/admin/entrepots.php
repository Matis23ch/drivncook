<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/Produit.php';

$entrepots = [
    1 => 'Paris – Produits frais',
    2 => 'Créteil – Plats préparés',
    3 => 'Nanterre – Boissons',
    4 => 'Versailles – Stock mixte'
];

if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
}

if (isset($_POST['nom'], $_POST['entrepot_id'], $_POST['origine'], $_POST['prix'], $_POST['quantite'])) {
    $data = [
        'nom' => $_POST['nom'],
        'entrepot_id' => $_POST['entrepot_id'],
        'origine' => $_POST['origine'],
        'stock' => (int)$_POST['quantite'],
        'prix' => (float)$_POST['prix']
    ];
    Produit::create($pdo, $data);
}

$tauxDC = Produit::tauxDC($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des entrepôts</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background-color: #f0f2f5;
        font-family: 'Inter', sans-serif;
    }

    .page-container {
        max-width: 1400px;
        margin: auto;
        padding: 2rem;
    }

    h1 {
        font-weight: 600;
    }

    .card {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-box-seam"></i> Gestion des entrepôts & produits</h1>
        <a href="dashboard.php" class="btn btn-outline-primary btn-rounded">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>

    <?php foreach ($entrepots as $id => $nomEntrepot): ?>

    <div class="card mb-5">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="bi bi-building"></i>
                    <?= htmlspecialchars($nomEntrepot) ?>
                </h4>
                <span class="badge bg-secondary">ID <?= $id ?></span>
            </div>

            <form method="POST" class="row g-3 mb-4">
                <input type="hidden" name="entrepot_id" value="<?= $id ?>">

                <div class="col-md-3">
                    <input name="nom" class="form-control" placeholder="Nom du produit" required>
                </div>

                <div class="col-md-3">
                    <input name="origine" class="form-control" placeholder="Origine" required>
                </div>

                <div class="col-md-2">
                    <input type="number" name="quantite" class="form-control" placeholder="Quantité" min="0" required>
                </div>

                <div class="col-md-2">
                    <input type="number" step="0.01" name="prix" class="form-control" placeholder="Prix (€)" min="0" required>
                </div>

                <div class="col-md-2 d-grid">
                    <button class="btn btn-success btn-rounded">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Origine</th>
                            <th>Stock</th>
                            <th>Prix unitaire</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach (Produit::getByEntrepot($pdo, $id) as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><?= htmlspecialchars($p['origine']) ?></td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= $p['stock'] ?>
                                </span>
                            </td>
                            <td><?= number_format($p['prix'], 2) ?> €</td>
                            <td class="text-center">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                                    <button class="btn btn-danger btn-sm btn-rounded"
                                            onclick="return confirm('Supprimer ce produit ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!Produit::getByEntrepot($pdo, $id)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Aucun produit dans cet entrepôt
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <?php endforeach; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



