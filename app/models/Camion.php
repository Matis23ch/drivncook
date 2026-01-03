<?php

class Camion {

    public static function all($pdo) {
        return $pdo->query("
            SELECT c.*, f.nom
            FROM camions c
            LEFT JOIN franchises f ON f.id = c.franchise_id
        ")->fetchAll();
    }

    public static function create($pdo, $franchise_id) {
        $stmt = $pdo->prepare("
            INSERT INTO camions (reference, etat, franchise_id)
            VALUES (?, 'En service', ?)
        ");
        return $stmt->execute(['DC-' . rand(100,999), $franchise_id]);
    }

    public static function updateEtat($pdo, $id, $etat) {
        $stmt = $pdo->prepare("UPDATE camions SET etat=? WHERE id=?");
        return $stmt->execute([$etat, $id]);
    }

    public static function getByFranchise($pdo, $franchise_id) {
        $stmt = $pdo->prepare("
            SELECT * FROM camions
            WHERE franchise_id = ?
            LIMIT 1
        ");
        $stmt->execute([$franchise_id]);
        return $stmt->fetch();
    }

}
