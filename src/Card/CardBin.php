<?php

namespace app\Card;

use app\Card\Handyapi;

class CardBin
{
    private static $binResults = [];
    const EUContry = ['AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR','HU','IE','IT','LT','LU','LV','MT','NL','PO','PT','RO','SE','SI','SK'];

    private function requestBin(int $bin)
    {
        $res = (new Handyapi())->getBinData($bin);
        return self::$binResults[$bin] = $res;
    }

    public function getBin(int $bin)
    {
        return self::$binResults[$bin] ?? $this->requestBin($bin);
    }

    public function isEU(int $bin)
    {
        return in_array(
            $this->getBin($bin)->countryA2, 
            self::EUContry
        );
    }
}