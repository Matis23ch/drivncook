<?php

class Commande {

    public static function calculTauxDC($pdo, $produits) {
        $total = count($produits);
        if ($total === 0) return 0;

        $dc = 0;
        foreach ($produits as $p) {
            if ($p['origine'] === 'DC') $dc++;
        }

        return round(($dc / $total) * 100, 2);
    }
}
