<?php

namespace App\Taxes;

class Detector
{
    protected $seuil;

    public function __construct($seuil)
    {
        $this->seuil = $seuil;
    }

    public function detect($prix)
    {
        if($prix > $this->seuil)
        {
            return true;
        }
        return false;
    }
}