<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$franchise_id = $_SESSION['franchise_id'];

$stmt = $pdo->prepare("
    INSERT INTO paiements (franchise_id, type, montant, date_paiement)
    VALUES (?, 'DROIT_ENTREE', 50000, NOW())
");
$stmt->execute([$franchise_id]);

header('Location: mon_camion.php');
exit;


