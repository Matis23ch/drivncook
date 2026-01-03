<?php

require_once __DIR__ . '/../models/Commande.php';

class CommandeController
{
    public static function create($data)
    {
        return Commande::create($data);
    }

    public static function getByFranchise($franchiseId)
    {
        return Commande::getByFranchise($franchiseId);
    }
}
