<?php

require_once __DIR__ . '/../models/Camion.php';

class CamionController
{
    public static function getAll()
    {
        return Camion::getAll();
    }

    public static function assignToFranchise($camionId, $franchiseId)
    {
        return Camion::assign($camionId, $franchiseId);
    }
}
