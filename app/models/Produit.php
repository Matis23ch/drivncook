<?php

class Produit {

    public static function getByEntrepot($pdo, $entrepot_id) {
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE entrepot_id = ?");
        $stmt->execute([$entrepot_id]);
        return $stmt->fetchAll();
    }

    public static function create($pdo, $data) {
        $stmt = $pdo->prepare("
            INSERT INTO produits (nom, entrepot_id, origine, stock, prix)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nom'],
            $data['entrepot_id'],
            $data['origine'],
            $data['stock'],    
            $data['prix']       
        ]);
    }

    public static function tauxDC($pdo) {
        $total = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
        if ($total == 0) return 0;

        $dc = $pdo->query("SELECT COUNT(*) FROM produits WHERE origine='DC'")->fetchColumn();
        return round(($dc / $total) * 100, 2);
    }
}
