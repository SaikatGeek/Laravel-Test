<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;
    

    public function test_homepage_contains_empty_products_table()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('No products found');
    }

    public function test_homepage_contains_non_empty_products_table()
    {
        $data = Product::create([
            "name" => "888",
            "price" => 56
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $view_products = $response->viewData('products')->first()->name;
        $response->assertDontSee('No products found');
        $this->assertEquals($view_products, $response->viewData('products')->first()->name);
    }

}
