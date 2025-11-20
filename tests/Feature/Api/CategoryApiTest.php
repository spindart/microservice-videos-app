<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category as CategoryModel;
use Illuminate\Http\Response;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class CategoryApiTest extends TestCase
{
    protected $endpoint = '/api/categories';

    public function testListEmptyCategories()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function testListCategories()
    {
        CategoryModel::factory()->count(30)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'per_page',
                'last_page',
                'from',
                'to',
                'first_page',
                'next_page',
                'previous_page',

            ]
        ]);
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
    }

    public function testListPaginateCategories()
    {
        CategoryModel::factory()->count(30)->create();

        $response = $this->getJson("$this->endpoint?page=2");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, $response['meta']['current_page']);
        $response->assertJsonCount(10, 'data');
    }

    public function testListCategoryNotFound()
    {
        $response = $this->getJson("$this->endpoint/fake_value");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListCategory()
    {
        $category = CategoryModel::factory()->create();
        $response = $this->getJson("$this->endpoint/{$category->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($category->name, $response['data']['name']);
    }

    public function testStoreCategory()
    {
        $data = [
            'name' => 'New Category',
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);

        $data2 = [
            'name' => 'New Category 2',
            'description' => 'Category Description',
        ];

        $response = $this->postJson($this->endpoint, $data2);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data2['name'], $response['data']['name']);
        $this->assertEquals($data2['description'], $response['data']['description']);

        $data3 = [
            'name' => 'New Category 3',
            'description' => 'Category Description',
            'is_active' => false,
        ];

        $response = $this->postJson($this->endpoint, $data3);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data3['name'], $response['data']['name']);
        $this->assertEquals($data3['description'], $response['data']['description']);
        $this->assertFalse($response['data']['is_active']);
    }

    public function testStoreCategoryInvalidData()
    {
        $data = [
            'name' => 'Ne',
            'description' => str_repeat('a', 300),
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'description'
            ]
        ]);
    }

    public function testUpdateCategory()
    {
        $category = CategoryModel::factory()->create();

        $data = [
            'name' => 'Updated Category',
            'description' => 'Updated Description',
        ];

        $response = $this->putJson("$this->endpoint/{$category->id}", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertEquals($data['description'], $response['data']['description']);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $data['name'],
            'description' => $data['description']
        ]);
    }

    public function testUpdateCategoryInvalidData()
    {
        $category = CategoryModel::factory()->create();

        $data = [
            'name' => 'Up',
            'description' => str_repeat('b', 300),
        ];

        $response = $this->putJson("$this->endpoint/{$category->id}", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'description'
            ]
        ]);
    }

    public function testUpdateCategoryNotFound()
    {
        $data = [
            'name' => 'Updated Category',
            'description' => 'Updated Description',
        ];

        $response = $this->putJson("$this->endpoint/fake_value", $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDestroyCategoryNotFound()
    {
        $response = $this->deleteJson("$this->endpoint/fake_value");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDestroyCategory()
    {
        $category = CategoryModel::factory()->create();

        $response = $this->deleteJson("$this->endpoint/{$category->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('categories', [
            'id' => $category->id
        ]);
    }
}
