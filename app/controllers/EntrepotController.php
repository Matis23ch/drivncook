<?php

require_once __DIR__ . '/../models/Entrepot.php';

class EntrepotController
{
    public static function getAll()
    {
        return Entrepot::getAll();
    }
}
