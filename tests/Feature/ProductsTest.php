<?php

namespace Tests\Feature;

use App\User; 
use App\Product;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_store_product_exists_in_database()
    {
        $response = $this->authorized(1)->post('/products', [
            'name' => 'Brand New Product',
            'price' => 99.99
        ]);

        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Brand New Product',
            'price' => 99.99
        ]);

        $product = Product::orderBy('id', 'desc')->first();

        $this->assertEquals('Brand New Product', $product->name);
        $this->assertEquals(99.99, $product->price);
    }

    public function test_edit_product_form_contains_correct_name_and_price()
    {
        $product = factory(Product::class)->create();

        $response = $this->authorized(1)->get('products/'.$product->id.'/edit');

        $response->assertStatus(200);

        $response->assertSee('value="'.$product->name.'"');
        $response->assertSee('value="'.$product->price.'"');
    }

    public function test_update_product_correct_validation_error()
    {
        $product = factory(Product::class)->create();

        $response = $this->authorized(1)->put('products/' . $product->id, [
            'name' => "test",
            'price' => 99.99
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_update_product_json_correct_validation_error()
    {
        $product = factory(Product::class)->create();

        $response = $this->authorized(1)->put('products/' . $product->id, 
            [ 'name' => "Test", 'price' => 99.99 ],
            ['Accept' => 'Application/json']
        );

        $response->assertStatus(422);
    }

    public function test_delete_product_no_longer_exists_in_database()
    {
        $product = factory(Product::class)->create();

        $this->assertEquals(1, Product::count());

        $response = $this->authorized(1)->delete("products/{$product->id}");

        $response->assertStatus(302);

        $this->assertEquals(0, Product::count());
    }

    public function test_create_product_file_upload()
    {
        Storage::fake('local');
        
        $file = UploadedFile::fake()->image('avatar.jpg');

        $this->authorized(1)->post('products', [
            'name' => 'Product with photo',
            'price' => 88.95,
            'photo' => $file
        ]);

        Storage::disk('local')->assertExists( 'logos/avatar.jpg' );
    }



}