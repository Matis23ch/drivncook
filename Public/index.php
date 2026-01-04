<?php
session_start();

/*
|--------------------------------------------------------------------------
| Détermination de la page demandée
|--------------------------------------------------------------------------
*/
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {

    // URL demandée
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // On enlève le chemin du projet
    $basePath = '/drivncook/public';
    $page = trim(str_replace($basePath, '', $uri), '/');

    // Racine = home
    if ($page === '' || $page === 'index.php') {
        $page = 'home';
    }
}

switch ($page) {

    case 'login':
        require 'login.php';
        break;

    case 'home':
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
        <meta charset="UTF-8">
        <title>DRIV'N COOK</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #6cc0ff, #009688);
            color: #fff;
        }

        .container-full {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .btn-login {
            font-size: 1.2rem;
            padding: 0.8rem 2rem;
            border-radius: 1rem;
            background-color: #fff;
            color: #009688;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #e0f2f1;
            color: #00695c;
            text-decoration: none;
        }
        </style>
        </head>
        <body>

        <div class="container-full">
            <h1>Bienvenue sur DRIV'N COOK</h1>
            <a href="/drivncook/public/login" class="btn btn-login">Se connecter</a>
        </div>

        </body>
        </html>
        <?php
        break;

    default:
        http_response_code(404);
        require 'errors/404.php';
        break;
}

