<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'FRANCHISE') {
    header('Location: ../login.php');
    exit;
}

$franchise_id = $_SESSION['franchise_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard Franchisé – DRIV’N COOK</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    html, body {
        height: 100%;
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: #f0f2f5;
    }

    .container-full {
        min-height: 100vh;
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .header h1 {
        font-weight: 600;
        margin: 0;
    }

    .welcome {
        color: #6c757d;
        margin-bottom: 2rem;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
        flex-grow: 1;
        align-content: start;
    }

    .menu-card {
        background-color: #ffffff;
        border-radius: 0.9rem;
        padding: 1.4rem;
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        text-decoration: none;
        color: inherit;

        display: flex;
        align-items: center;
        gap: 1rem;

        transition: all .2s ease;
    }

    .menu-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.12);
    }

    .menu-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #e9f1ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #0d6efd;
        flex-shrink: 0;
    }

    .menu-text {
        font-size: 1.05rem;
        font-weight: 500;
    }

    .logout-btn {
        border-radius: .75rem;
        padding: .45rem 1rem;
    }
</style>
</head>

<body>

<div class="container-full">

    <div class="header">
        <h1>Dashboard Franchisé</h1>
        <form action="../logout.php" method="POST">
            <button type="submit" class="btn btn-outline-danger logout-btn">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </button>
        </form>
    </div>

    <p class="welcome">Bienvenue sur votre espace franchisé.</p>

    <div class="menu-grid">

        <a href="mon_camion.php" class="menu-card">
            <div class="menu-icon">
                <i class="bi bi-truck"></i>
            </div>
            <div class="menu-text">Mon camion</div>
        </a>

        <a href="commandes.php" class="menu-card">
            <div class="menu-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="menu-text">Mes commandes</div>
        </a>

        <a href="ventes.php" class="menu-card">
            <div class="menu-icon">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="menu-text">Mes ventes</div>
        </a>

        <a href="mes_factures.php" class="menu-card">
            <div class="menu-icon">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="menu-text">Mes factures</div>
        </a>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



