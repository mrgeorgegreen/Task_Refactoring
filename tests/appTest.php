<?php

use PHPUnit\Framework\TestCase;
use app\Services\CommisionCalculation;
use app\Card\CardBin;
use app\Currency\CurrencyRate;

class CommisionCalculationTest extends TestCase
{
    public function testHandle()
    {
        $cardBinMock = $this->createMock(CardBin::class);
        $cardBinMock->method('isEU')
            ->will($this->returnValueMap([
                [45717360, true],  // Example EU bin
                [516793, false]    // Example non-EU bin
            ]));
        
        $currencyRateMock = $this->createMock(CurrencyRate::class);
        $currencyRateMock->method('getRate')
            ->will($this->returnValueMap([
                ['EUR', 1],          // EUR has a rate of 1
                ['USD', 1.2],        // Example rate for USD
            ]));

        $commissionCalculation = $this->getMockBuilder(CommisionCalculation::class)
                                      ->setConstructorArgs([$cardBinMock, $currencyRateMock])
                                      ->onlyMethods(['loadFile', 'outPut'])
                                      ->getMock();

        $commissionCalculation->method('loadFile')
             ->willReturn([
                 (object)['bin' => '45717360', 'amount' => 100, 'currency' => 'EUR'],
                 (object)['bin' => '516793', 'amount' => 200, 'currency' => 'USD'],
             ]);

        $this->expectOutputString("1\n3.33\n");

        $commissionCalculation->handle('dummy_path');
    }
}
