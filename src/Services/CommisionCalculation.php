<?php

namespace app\Services;

use app\Card\CardBin;
use app\Currency\CurrencyRate;

class CommisionCalculation
{
    const Commision_Rate_EU = 0.01;
    const Commision_Rate_NON_EU = 0.02;
    const Currency_EU = 'EUR';

    private $cardBin;
    private $currencyRate;

    public function __construct(CardBin $cardBin, CurrencyRate $currencyRate)
    {
        $this->cardBin = $cardBin;
        $this->currencyRate = $currencyRate;
    }

    protected function loadFile(string $path)
    {
        $data = [];

        $file = new \SplFileObject($path);
        while (!$file->eof()) {
            $data[] = json_decode($file->fgets());
        }
        $file = null;

        return $data;
    }

    private function outPut(array $results)
    {
        foreach($results as $row)
        {
            echo $row;
            print "\n";
        }
    }

    public function handle(string $path)
    {
        $results = [];

        foreach($this->loadFile($path) as $row)
        {
            $isEu = $this->cardBin->isEU($row->bin);
            $rate = $this->currencyRate->getRate($row->currency);

            if ($row->currency === self::Currency_EU || $rate == 0) {
                $amntFixed = $row->amount;
            } else {
                $amntFixed = $row->amount / $rate;
            }
    
            $results[] = floor($amntFixed * ($isEu ? self::Commision_Rate_EU : self::Commision_Rate_NON_EU) * 100) / 100;
        }

        $this->outPut($results);
    }
}
