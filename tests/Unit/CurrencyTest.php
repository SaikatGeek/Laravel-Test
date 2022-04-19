<?php

namespace Tests\Unit;

use App\Services\CurrencyService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurrencyTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_currency_convert_usd_to_eur()
    {
        $usd_amount = 100;

        $this->assertEquals(89, 
            (new CurrencyService)->convert($usd_amount, 'usd', 'eur')
        );
    }
    
}
