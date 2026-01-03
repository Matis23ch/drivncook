<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/Produit.php';

$entrepots = [
    1 => 'Paris – Produits frais',
    2 => 'Créteil – Plats préparés',
    3 => 'Nanterre – Boissons',
    4 => 'Versailles – Stock mixte'
];

// Supprimer un produit
if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
}

// Ajouter un produit
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

// Taux DC si nécessaire
$tauxDC = Produit::tauxDC($pdo);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Gestion des entrepôts & produits</h1>

<?php foreach ($entrepots as $id => $nomEntrepot): ?>
<h3><?= htmlspecialchars($nomEntrepot) ?></h3>

<!-- Formulaire ajout produit -->
<form method="POST" class="row g-2 mb-3">
    <input type="hidden" name="entrepot_id" value="<?= $id ?>">
    <input name="nom" class="form-control col" placeholder="Nom du produit" required>
    <input name="origine" class="form-control col" placeholder="Origine" required>
    <input type="number" name="quantite" class="form-control col" placeholder="Quantité" min="0" required>
    <input type="number" step="0.01" name="prix" class="form-control col" placeholder="Prix (€)" min="0" required>
    <button class="btn btn-success col-2">Ajouter</button>
</form>

<!-- Tableau des produits existants -->
<table class="table table-bordered table-sm">
<tr>
    <th>Produit</th>
    <th>Origine</th>
    <th>Quantité</th>
    <th>Prix unitaire (€)</th>
    <th>Action</th>
</tr>
<?php foreach (Produit::getByEntrepot($pdo, $id) as $p): ?>
<tr>
    <td><?= htmlspecialchars($p['nom']) ?></td>
    <td><?= htmlspecialchars($p['origine']) ?></td>
    <td><?= $p['stock'] ?></td>
    <td><?= number_format($p['prix'], 2) ?></td>
    <td>
        <form method="POST" style="display:inline">
            <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
            <button class="btn btn-sm btn-danger">Supprimer</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php endforeach; ?>

<a href="dashboard.php" class="btn btn-primary mt-3">Retour au Dashboard</a>

</body>
</html>



