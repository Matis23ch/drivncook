<?php
session_start();

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login':
        require 'login.php';
        break;
    default:
        echo "<h1>Bienvenue sur DRIV'N COOK</h1>";
        echo "<a href='?page=login'>Se connecter</a>";
        break;
}
