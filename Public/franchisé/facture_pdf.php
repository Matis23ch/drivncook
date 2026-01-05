<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/database.php';

use Dompdf\Dompdf;

// Vérification rôle
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION['role'] !== 'FRANCHISE') exit;

$commande_id = (int)($_GET['id'] ?? 0);
$franchise_id = $_SESSION['franchise_id'];

/* ===========================
   Récupérer les infos de la commande
=========================== */
$stmt = $pdo->prepare("
    SELECT *
    FROM commandes
    WHERE id = ? AND franchise_id = ?
");
$stmt->execute([$commande_id, $franchise_id]);
$commande = $stmt->fetch();

if (!$commande) exit;

$stmt = $pdo->prepare("
    SELECT cl.*, p.nom
    FROM commande_lignes cl
    LEFT JOIN produits p ON p.id = cl.produit_id
    WHERE cl.commande_id = ?
");
$stmt->execute([$commande_id]);
$lignes = $stmt->fetchAll();

/* ===========================
   Générer le HTML pour le PDF
=========================== */
$html = '<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
table { border-collapse: collapse; width: 100%; margin-top: 10px; }
th, td { border: 1px solid #000; padding: 5px; text-align: left; }
th { background-color: #f2f2f2; }
h1 { text-align: center; }
</style>
</head>
<body>
<h1>Facture DRIV\'N COOK</h1>
<p><strong>Date :</strong> '.date('d/m/Y', strtotime($commande['date_commande'])).'<br>
<strong>Taux DC :</strong> '.$commande['taux_dc'].' %</p>
<table>
<tr>
<th>Produit</th><th>Type</th><th>Quantité</th><th>Montant</th>
</tr>';

$totalDC = 0;

// ⚡ On ne prend en compte que les produits DC
foreach ($lignes as $l) {
    if ($l['type'] !== 'DC') continue; // ignore produits libres

    $ligneTotal = $l['quantite'] * $l['prix'];
    $totalDC += $ligneTotal;

    $nom = htmlspecialchars($l['nom'] ?? 'Produit DC', ENT_QUOTES, 'UTF-8');
    $html .= "<tr>
    <td>$nom</td>
    <td>{$l['type']}</td>
    <td>{$l['quantite']}</td>
    <td>".number_format($ligneTotal,2)." €</td>
    </tr>";
}

$html .= '<tr>
<th colspan="3" style="text-align:right;">TOTAL DC</th>
<th>'.number_format($totalDC,2).' €</th>
</tr>
</table>
</body>
</html>';

/* ===========================
   Générer le PDF
=========================== */
$dompdf = new Dompdf([
    'isRemoteEnabled' => true
]);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

/* téléchargement */
$dompdf->stream("facture_$commande_id.pdf", ["Attachment" => true]);
exit;



