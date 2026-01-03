<?php

require_once __DIR__ . '/../models/Franchisé.php';

class FranchiséController
{
    public static function index(PDO $pdo)
    {
        $stmt = $pdo->query("
            SELECT f.id, f.nom, f.email, f.actif,
                   c.reference AS camion_reference,
                   EXISTS (
                       SELECT 1 FROM paiements p
                       WHERE p.franchise_id = f.id
                       AND p.type = 'DROIT_ENTREE'
                   ) AS droit_paye,
                   IFNULL(SUM(v.montant),0) AS ca_total
            FROM franchises f
            LEFT JOIN camions c ON c.franchise_id = f.id
            LEFT JOIN ventes v ON v.franchise_id = f.id
            WHERE f.actif = 1
            GROUP BY f.id
        ");

        return $stmt->fetchAll();
    }

    public static function store(PDO $pdo, array $data)
    {
        Franchisé::create($pdo, $data);
    }

    public static function destroy(PDO $pdo, int $id)
    {
        Franchisé::desactiver($pdo, $id);
    }
}



