<?php

namespace Tests\Feature;

use App\User; 
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    private $authorized;

    public function setUp():void
    {
        parent::setUp();

        $user = factory(User::class)->create([
            'email' => 'test@laravel.com',
            'password' => bcrypt('test_phase_squad')
        ]);

        $this->authorized = $this->actingAs($user);
    }

    public function auth()
    {
        $user = factory(User::class)->create([
            'email' => 'test@laravel.com',
            'password' => bcrypt('test_phase_squad')
        ]);

        return $this->actingAs($user);
    }
    

    public function test_homepage_contains_empty_products_table()
    {
        $response = $this->authorized->get('/');

        $response->assertStatus(200);
        
        $response->assertSee('No products found');
    }

    public function test_homepage_contains_non_empty_products_table()
    {
        $data = Product::create([
            "name" => "888",
            "price" => 56
        ]);

        $response = $this->authorized->get('/');

        $response->assertStatus(200);

        $view_products = $response->viewData('products')->first()->name;

        $this->assertEquals($view_products, $data->name);
    }

    public function test_paginated_products_table_doesnt_show_11th_record()
    {
        $products = factory(Product::class, 11)->create([ 'price' => '99.99' ]);
        info($products);

        $response = $this->get('/');
        
        $response->assertDontSee($products->last()->name);
    }


}
