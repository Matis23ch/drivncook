<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Franchisé</title>
</head>
<body>

<h1>Bienvenue Franchisé</h1>

<ul>
    <li><a href="mon_camion.php">Mon camion</a></li>
    <li><a href="commandes.php">Mes commandes</a></li>
    <li><a href="ventes.php">Mes ventes</a></li>
</ul>

<a href="../logout.php">Déconnexion</a>

</body>
</html>

