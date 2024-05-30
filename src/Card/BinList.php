<?php

namespace app\Card;

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
            throw new \Exception('BinList data is empty');

        return (object) array('countryA2' => $rowData->country->alpha2);
    }
}