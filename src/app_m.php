<?php

class BinList
{
    private function requestBin(int $bin)
    {
        return json_decode(
            file_get_contents('https://lookup.binlist.net/'.$bin)
        );
    }
    
    public function getBinData(int $bin)
    {
        $rowData = $this->requestBin($bin);
        if (!$rowData)
            die('error!'); // todo

        return (object) array('countryA2' => $rowData->country->alpha2);
    }
}

class Handyapi
{
    private function requestBin(int $bin)
    {
        return json_decode(
            file_get_contents('https://data.handyapi.com/bin/'.$bin)
        );
    }

    public function getBinData(int $bin)
    {
        $rowData = $this->requestBin($bin);
        if (!$rowData)
            die('error!'); //todo

        return (object) array('countryA2' => $rowData->Country->A2);
    }
}

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


class app
{
    static public function run(string $path)
    {
        $cardBin = new CardBin();
        $currencyRate = new CurrencyRate();

        $file = new SplFileObject($path);
        while (!$file->eof()) {
            $js = json_decode($file->fgets());

            $isEu = $cardBin->isEU($js->bin);
            $rate = $currencyRate->getRate($js->currency);

            if ($js->currency == 'EUR' or $rate == 0) {
                $amntFixed = $js->amount;
            }
            if ($js->currency != 'EUR' or $rate > 0) {
                $amntFixed = $js->amount / $rate;
            }
    
            echo $amntFixed * ($isEu == 'yes' ? 0.01 : 0.02);
            print "\n";        
        }
        $file = null;
    }
}

app::run($argv[1]);
