<?php

require_once __DIR__ . '/../models/Vente.php';

class VenteController
{
    public static function getByFranchise($franchiseId)
    {
        return Vente::getByFranchise($franchiseId);
    }

    public static function getGlobal()
    {
        return Vente::getGlobal();
    }
}
