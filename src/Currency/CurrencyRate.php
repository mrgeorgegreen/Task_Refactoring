<?php

namespace app\Currency;

use app\Currency\Paysera;

class CurrencyRate
{
    private static $currencyRates = [];

    private function requestRates()
    {
        self::$currencyRates = (new Paysera)->getCurrencyRateData();
    }

    public function getRate(string $currency)
    {
        if(!count(self::$currencyRates))
        {
            $this->requestRates();
        }

        return self::$currencyRates[$currency];
    }

}