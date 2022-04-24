<?php

namespace Tests\Feature;

use App\User; 
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;


    public function authorized($is_admin = 0)
    {
        $user = factory(User::class)->create([
            'email' => $is_admin ? 'admin@laravel.com' : 'test@laravel.com',
            'password' => bcrypt('test_phase_squad'),
            'is_admin' => $is_admin
        ]);

        return $this->actingAs($user);
    }

    public function test_homepage_contains_empty_products_table()
    {
        $response = $this->authorized(0)->get('/products');

        $response->assertStatus(200);
        
        $response->assertSee('No products found');
    }

    public function test_homepage_contains_non_empty_products_table()
    {
        $data = Product::create([
            "name" => "888",
            "price" => 56
        ]);

        $response = $this->authorized(0)->get('/products');

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

    public function test_admin_can_see_product_create_button()
    {
        $response = $this->authorized(1)->get('/products');
        
        $response->assertStatus(200);

        $response->assertSee('Add new product');
    }

    public function test_non_admin_user_cannot_see_product_create_button()
    {
        $response = $this->authorized(0)->get('/products');
        
        $response->assertStatus(200);

        $response->assertDontSee('Add new product');
    }

    public function test_admin_can_access_products_create_page()
    {
        $response = $this->authorized(1)->get('/products/create');

        $response->assertStatus(200);
    }

    public function test_non_admin_user_can_not_access_products_create_page()
    {
        $response = $this->authorized(0)->get('/products/create');

        $response->assertStatus(403);
    }


}
