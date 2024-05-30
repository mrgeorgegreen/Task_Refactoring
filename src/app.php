<?php

namespace app;

use app\Services\CommisionCalculation;
use app\Card\CardBin;
use app\Currency\CurrencyRate;

require __DIR__ . '/../vendor/autoload.php';

if ($argv[1])
{
    (new CommisionCalculation(
        new CardBin(), 
        new CurrencyRate()
    ))->handle($argv[1]);
}

// Error hendling
set_exception_handler(function(\Throwable $exception){
    echo "Uncaught exception: " , $exception->getMessage(), "\n";
});