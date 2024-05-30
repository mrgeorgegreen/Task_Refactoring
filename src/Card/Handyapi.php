<?php

namespace app\Card;

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
            throw new \Exception('HandyAPI data is empty');

        return (object) array('countryA2' => $rowData->Country->A2);
    }
}