<?php
require_once __DIR__ . '/../models/Produit.php';

class ProduitController {

    public static function index($pdo) {
        return Produit::all($pdo);
    }

    public static function store($pdo, $data) {
        Produit::create($pdo, $data);
    }

    public static function tauxDC($pdo) {
        return Produit::tauxDC($pdo);
    }
}
