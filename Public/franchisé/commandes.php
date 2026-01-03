<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

/* Produits Driv’n Cook */
$produits = $pdo->query("
    SELECT id, nom, prix, stock
    FROM produits
")->fetchAll(PDO::FETCH_ASSOC);

$taux = null;
$valide = false;
$recap = [];

if ($_POST) {

    $totalDC = 0;
    $totalGlobal = 0;

    foreach ($produits as $p) {
        $qte = (int)($_POST['qte_' . $p['id']] ?? 0);

        if ($qte > $p['stock']) {
            die("Quantité supérieure au stock pour {$p['nom']}");
        }

        if ($qte > 0) {
            $totalDC += $qte * $p['prix'];
            $totalGlobal += $qte * $p['prix'];
            $recap[] = [
                'type' => 'DC',
                'produit_id' => $p['id'],
                'nom' => $p['nom'],
                'quantite' => $qte,
                'prix' => $p['prix']
            ];
        }
    }

    /* Produits libres : PRIX TOTAL */
    $qteLibre  = (int)($_POST['qte_libre'] ?? 0);
    $prixLibre = (float)($_POST['prix_libre'] ?? 0);

    if ($qteLibre > 0 && $prixLibre > 0) {
        $totalGlobal += $prixLibre;
        $recap[] = [
            'type' => 'LIBRE',
            'produit_id' => null,
            'nom' => 'Produit libre hors Driv’n Cook',
            'quantite' => $qteLibre,
            'prix' => $prixLibre
        ];
    }

    if ($totalGlobal > 0) {
        $taux = round(($totalDC / $totalGlobal) * 100, 2);
        $valide = $taux >= 80;
    }

    /* VALIDATION */
    if (isset($_POST['valider']) && $valide) {

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO commandes (franchise_id, date_commande, taux_dc)
                VALUES (?, NOW(), ?)
            ");
            $stmt->execute([$franchise_id, $taux]);
            $commande_id = $pdo->lastInsertId();

            foreach ($recap as $r) {

                $stmt = $pdo->prepare("
                    INSERT INTO commande_lignes
                    (commande_id, produit_id, quantite, prix, type)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $commande_id,
                    $r['produit_id'],
                    $r['quantite'],
                    $r['prix'],
                    $r['type']
                ]);

                if ($r['type'] === 'DC') {
                    $pdo->prepare("
                        UPDATE produits
                        SET stock = stock - ?
                        WHERE id = ?
                    ")->execute([$r['quantite'], $r['produit_id']]);
                }
            }

            $pdo->prepare("
                UPDATE commandes
                SET total = (
                    SELECT SUM(
                        CASE 
                            WHEN type = 'LIBRE' THEN prix
                            ELSE quantite * prix
                        END
                    )
                    FROM commande_lignes
                    WHERE commande_id = ?
                )
                WHERE id = ?
            ")->execute([$commande_id, $commande_id]);

            $pdo->commit();
            header("Location: facture.php?id=$commande_id");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur commande : " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container">

<a href="dashboard.php" class="btn btn-outline-secondary mb-3">← Dashboard</a>

<h1>Passer une commande</h1>

<form method="POST">

<h4>Produits Driv’n Cook</h4>

<?php foreach ($produits as $p): ?>
<div class="row mb-2">
    <div class="col">
        <?= htmlspecialchars($p['nom']) ?>
        <small class="text-muted">
            (<?= number_format($p['prix'],2) ?> € — stock <?= $p['stock'] ?>)
        </small>
    </div>
    <div class="col-3">
        <input type="number"
               name="qte_<?= $p['id'] ?>"
               min="0"
               max="<?= $p['stock'] ?>"
               class="form-control"
               value="<?= $_POST['qte_'.$p['id']] ?? 0 ?>">
    </div>
</div>
<?php endforeach; ?>

<hr>

<div class="card border-warning p-3">
<h5 class="text-warning">Produits libres (hors Driv’n Cook)</h5>
<input type="number" name="qte_libre" class="form-control mb-2" placeholder="Quantité totale">
<input type="number" step="0.01" name="prix_libre" class="form-control" placeholder="Prix total">
</div>

<button class="btn btn-primary mt-3">Calculer</button>

<?php if ($taux !== null): ?>
<div class="alert <?= $valide ? 'alert-success' : 'alert-danger' ?> mt-3">
Taux Driv’n Cook : <?= $taux ?> %
</div>

<?php if ($valide): ?>
<button name="valider" class="btn btn-success">Valider la commande</button>
<?php endif; ?>
<?php endif; ?>

</form>
</body>
</html>




