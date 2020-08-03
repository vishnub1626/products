<?php

namespace Tests\Feature;

use App\Product;
use App\User;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    const API_ENDPOINT = '/api/v1/products/import';

    /** @test */
    public function only_logged_user_can_import_products()
    {
        $this->json('POST', self::API_ENDPOINT, [
            'file' => UploadedFile::fake()->create('products.csv'),
        ])->assertStatus(401);
    }

    /** @test */
    public function a_file_is_required()
    {
        Sanctum::actingAs(
            factory(User::class)->create(),
            ['*']
        );

        $this->json('POST', self::API_ENDPOINT, [
            'file' => '',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'file' => [
                        'The file field is required.'
                    ]
                ]
            ]);
    }

    /** @test */
    public function file_must_be_a_valid_file()
    {
        Sanctum::actingAs(
            factory(User::class)->create(),
            ['*']
        );

        $this->json('POST', self::API_ENDPOINT, [
            'file' => 'asdasda',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'file' => [
                        'The file must be a file.'
                    ]
                ]
            ]);
    }

    /** @test */
    public function file_must_be_a_valid_csv_file()
    {
        Sanctum::actingAs(
            factory(User::class)->create(),
            ['*']
        );

        $this->json('POST', self::API_ENDPOINT, [
            'file' => UploadedFile::fake()->image('test.png'),
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'file' => [
                        'The file must be a file of type: csv, txt.'
                    ]
                ]
            ]);
    }

    /** @test */
    public function a_logged_in_user_can_export_products()
    {
        Sanctum::actingAs(
            factory(User::class)->create(),
            ['*']
        );

        $this->json('POST', self::API_ENDPOINT, [
            'file' => new UploadedFile(base_path('tests/Feature/files/products.csv'), 'products.csv', 'csv', null, true),
        ])->assertStatus(200)
        ->assertJson([
            'message' => 'Products imported successfully.'
        ]);

        $this->assertCount(2, Product::all());
    }
}
