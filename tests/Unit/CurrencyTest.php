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
    public function test_currency_convert_usd_to_euro()
    {
        $amount_in_usd = 100;

        $this->assertEquals( 89, 
            (new CurrencyService)->convert($amount_in_usd, 'usd', 'eur')
        );
    }

    public function test_currency_convert_gbp_to_euro()
    {
        $amount_in_gbp = 100;

        $this->assertEquals( 0, 
            (new CurrencyService)->convert($amount_in_gbp, 'gbp', 'euro')
        );
    }

    public function test_currency_convert_usd_to_gbp()
    {
        $amount_in_usd = 100;

        $this->assertEquals( 0,
            (new CurrencyService)->convert($amount_in_usd, 'usd', 'gbp')
        );
    }
    
}
