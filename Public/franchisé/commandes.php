<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

/* 🔒 Vérifier camion attribué */
$checkCamion = $pdo->prepare("SELECT id FROM camions WHERE franchise_id = ?");
$checkCamion->execute([$franchise_id]);
if (!$checkCamion->fetch()) {
    die("<div class='alert alert-danger'>Vous devez avoir un camion attribué pour commander.</div>");
}

/* Produits DC */
$produits = $pdo->query("SELECT id, nom, prix, stock FROM produits")->fetchAll();

$taux = null;
$valide = false;
$totalDC = 0;
$totalGlobal = 0;

if ($_POST && isset($_POST['calculer'])) {

    foreach ($produits as $p) {
        $qte = (int)($_POST['qte_' . $p['id']] ?? 0);
        if ($qte > 0) {
            $totalDC += $qte;
            $totalGlobal += $qte;
        }
    }

    $qteLibre = (int)($_POST['qte_libre'] ?? 0);
    if ($qteLibre > 0) {
        $totalGlobal += $qteLibre;
    }

    if ($totalGlobal > 0) {
        $taux = round(($totalDC / $totalGlobal) * 100, 2);
        $valide = $taux >= 80;
    }
}

/* ✅ Validation commande */
if ($_POST && isset($_POST['valider']) && $_POST['taux'] >= 80) {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO commandes (franchise_id, date_commande, taux_dc)
        VALUES (?, NOW(), ?)
    ");
    $stmt->execute([$franchise_id, $_POST['taux']]);
    $commande_id = $pdo->lastInsertId();

    foreach ($produits as $p) {
        $qte = (int)($_POST['qte_' . $p['id']] ?? 0);
        if ($qte > 0) {

            if ($qte > $p['stock']) {
                $pdo->rollBack();
                die("Stock insuffisant pour {$p['nom']}");
            }

            // ligne commande
            $stmt = $pdo->prepare("
                INSERT INTO commande_lignes
                (commande_id, produit_id, quantite, prix, type)
                VALUES (?, ?, ?, ?, 'DC')
            ");
            $stmt->execute([$commande_id, $p['id'], $qte, $p['prix']]);

            // décrément stock
            $stmt = $pdo->prepare("
                UPDATE produits SET stock = stock - ?
                WHERE id = ?
            ");
            $stmt->execute([$qte, $p['id']]);
        }
    }

    // Produits libres
    if ($_POST['qte_libre'] > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO commande_lignes
            (commande_id, produit_id, quantite, prix, type)
            VALUES (?, NULL, ?, ?, 'LIBRE')
        ");
        $stmt->execute([
            $commande_id,
            $_POST['qte_libre'],
            $_POST['prix_libre']
        ]);
    }

    $pdo->commit();
    header('Location: mes_achats.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

<h1>Passer une commande</h1>

<form method="POST">

<h4>Produits Driv’n Cook</h4>
<?php foreach ($produits as $p): ?>
<div class="row mb-2">
    <div class="col"><?= $p['nom'] ?> (stock: <?= $p['stock'] ?>)</div>
    <div class="col-3">
        <input type="number" name="qte_<?= $p['id'] ?>" min="0" value="0" class="form-control">
    </div>
</div>
<?php endforeach; ?>

<hr>

<div class="card p-3 border-warning">
<h5 class="text-warning">Produits libres (hors DC)</h5>
<input type="number" name="qte_libre" class="form-control mb-2" placeholder="Quantité">
<input type="number" step="0.01" name="prix_libre" class="form-control" placeholder="Prix unitaire">
</div>

<button name="calculer" class="btn btn-primary mt-3">Calculer</button>

<?php if ($taux !== null): ?>
<div class="alert <?= $valide ? 'alert-success' : 'alert-danger' ?> mt-3">
Taux DC : <?= $taux ?> %
</div>

<?php if ($valide): ?>
<input type="hidden" name="taux" value="<?= $taux ?>">
<button name="valider" class="btn btn-success">Valider la commande</button>
<?php endif; ?>
<?php endif; ?>

</form>

</body>
</html>




