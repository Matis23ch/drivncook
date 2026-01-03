<?php

class Paiement {

    public static function payerDroitEntree($pdo, $franchise_id) {
        $stmt = $pdo->prepare("
            INSERT INTO paiements (franchise_id, type, montant, date_paiement)
            VALUES (?, 'DROIT_ENTREE', 50000, NOW())
        ");
        return $stmt->execute([$franchise_id]);
    }

    public static function droitEntreePaye($pdo, $franchise_id) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM paiements
            WHERE franchise_id=? AND type='DROIT_ENTREE'
        ");
        $stmt->execute([$franchise_id]);
        return $stmt->fetchColumn() > 0;
    }

    public static function payerRedevance($pdo, $franchise_id, $montant) {
        $stmt = $pdo->prepare("
            INSERT INTO paiements (franchise_id, type, montant, date_paiement)
            VALUES (?, 'REDEVANCE', ?, NOW())
        ");
        return $stmt->execute([$franchise_id, $montant]);
    }
}

