<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];

$produits = $pdo->query("
    SELECT id, nom, prix, stock
    FROM produits
")->fetchAll(PDO::FETCH_ASSOC);

$taux = null;
$valide = false;
$recap = [];

if ($_POST) {

    $totalQteDC = 0;
    $totalQteLibre = 0;

    foreach ($produits as $p) {
        $qte = (int)($_POST['qte_' . $p['id']] ?? 0);

        if ($qte > $p['stock']) {
            die("Quantité supérieure au stock pour {$p['nom']}");
        }

        if ($qte > 0) {
            $totalQteDC += $qte;
            $recap[] = [
                'type' => 'DC',
                'produit_id' => $p['id'],
                'nom' => $p['nom'],
                'quantite' => $qte,
                'prix' => $p['prix']
            ];
        }
    }

    $qteLibre  = (int)($_POST['qte_libre'] ?? 0);
    $prixLibre = (float)($_POST['prix_libre'] ?? 0);

    if ($qteLibre > 0 && $prixLibre > 0) {
        $totalQteLibre += $qteLibre;
        $recap[] = [
            'type' => 'LIBRE',
            'produit_id' => null,
            'nom' => 'Produit libre hors Driv’n Cook',
            'quantite' => $qteLibre,
            'prix' => $prixLibre
        ];
    }

    $totalQte = $totalQteDC + $totalQteLibre;

    if ($totalQte > 0) {
        $taux = round(($totalQteDC / $totalQte) * 100, 2);
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
<title>Passer une commande</title>
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
    font-weight: 600;
    margin-bottom: 1rem;
}

.row.mb-2 {
    align-items: center;
}

input.form-control, select.form-control {
    border-radius: 0.8rem;
}

.card {
    border-radius: 0.8rem;
    padding: 1rem;
}

.btn-modern {
    border-radius: 0.8rem;
    padding: 0.6rem 1.5rem;
    font-size: 1rem;
}

.bottom-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.bottom-buttons .btn-modern {
    flex: 1;
}
</style>
</head>

<body>

<a href="dashboard.php" class="btn btn-outline-secondary mb-3">← Dashboard</a>

<h1>Passer une commande</h1>

<form method="POST">

<h4>Produits Driv’n Cook</h4>

<?php foreach ($produits as $p): ?>
<div class="row mb-2">
    <div class="col">
        <?= htmlspecialchars($p['nom']) ?>
        <small class="text-muted">(<?= number_format($p['prix'],2) ?> € — stock <?= $p['stock'] ?>)</small>
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

<div class="card border-warning mb-3">
<h5 class="text-warning">Produits libres (hors Driv’n Cook)</h5>
<input type="number" name="qte_libre" class="form-control mb-2" placeholder="Quantité totale">
<input type="number" step="0.01" name="prix_libre" class="form-control" placeholder="Prix total">
</div>

<button class="btn btn-primary btn-modern mt-2">Calculer</button>

<?php if ($taux !== null): ?>
<div class="alert <?= $valide ? 'alert-success' : 'alert-danger' ?> mt-3">
Taux Driv’n Cook : <?= $taux ?> %
</div>

<?php if ($valide): ?>
<div class="bottom-buttons">
    <button name="valider" class="btn btn-success btn-modern">
        Valider la commande
    </button>
    <a href="dashboard.php" class="btn btn-secondary btn-modern">
        Annuler
    </a>
</div>
<?php endif; ?>
<?php endif; ?>

</form>

</body>
</html>






