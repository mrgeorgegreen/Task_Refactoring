<?php

namespace app\Currency;

class Paysera
{
    private function requestRate()
    {
        return file_get_contents('https://developers.paysera.com/tasks/api/currency-exchange-rates');
    }
    
    public function getCurrencyRateData()
    {
        return json_decode($this->requestRate(), true)['rates'];
    }
}
