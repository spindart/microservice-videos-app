<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Genre as GenreModel;
use App\Models\Category as CategoryModel;
use Illuminate\Http\Response;

class GenreApiTest extends TestCase
{
    protected $endpoint = '/api/genres';

    public function testIndexEmptyGenres()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
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
    }

    public function testIndex()
    {
        GenreModel::factory()->count(30)->create();

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
        $response->assertJsonFragment(['total' => 30]);
    }

    public function testIndexPaginate()
    {
        GenreModel::factory()->count(30)->create();

        $response = $this->getJson("$this->endpoint?page=2");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, $response['meta']['current_page']);
        $response->assertJsonCount(10, 'data');
    }

    public function testShowNotFound()
    {
        $response = $this->getJson("$this->endpoint/fake_value");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShow()
    {
        $genre = GenreModel::factory()->create();
        $response = $this->getJson("$this->endpoint/{$genre->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($genre->name, $response['data']['name']);
    }

    public function testStoreGenre()
    {
        $data = [
            'name' => 'New Genre',
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);


        $data2 = [
            'name' => 'New Genre 3',
            'is_active' => false,
        ];

        $response = $this->postJson($this->endpoint, $data2);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data2['name'], $response['data']['name']);
        $this->assertFalse($response['data']['is_active']);
    }

    public function testStoreGenreWithCategories()
    {
        $categories = CategoryModel::factory()->count(3)->create()->pluck('id')->toArray();

        $data = [
            'name' => 'New Genre with Categories',
            'categories_id' => $categories,
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);

        foreach ($categories as $categoryId) {
            $this->assertDatabaseHas('category_genre', [
                'genre_id' => $response['data']['id'],
                'category_id' => $categoryId,
            ]);
        }
    }

    public function testValidationStoreWithInvalidCategories()
    {

        $categories = [999, 888];

        $data = [
            'name' => 'New Genre with Categories',
            'categories_id' => $categories,
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'categories_id',
            ]
        ]);
    }

    public function testStoreGenreInvalidData()
    {
        $data = [
            'name' => 'Ne',
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
            ]
        ]);
    }

    public function testUpdateGenre()
    {
        $genre = GenreModel::factory()->create();

        $data = [
            'name' => 'Updated Genre',
            'categories_id' => [],
        ];

        $response = $this->putJson("$this->endpoint/{$genre->id}", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => $data['name'],
        ]);
    }

    public function testUpdateGenreWithCategories()
    {
        $genre = GenreModel::factory()->create();
        $categories = CategoryModel::factory()->count(2)->create()->pluck('id')->toArray();

        $data = [
            'name' => 'Updated Genre with Categories',
            'categories_id' => $categories,
        ];

        $response = $this->putJson("$this->endpoint/{$genre->id}", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);

        foreach ($categories as $categoryId) {
            $this->assertDatabaseHas('category_genre', [
                'genre_id' => $genre->id,
                'category_id' => $categoryId,
            ]);
        }
    }

    public function testUpdateGenreInvalidName()
    {
        $genre = GenreModel::factory()->create();

        $data = [
            'name' => 'Up',
        ];

        $response = $this->putJson("$this->endpoint/{$genre->id}", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
            ]
        ]);
    }
    public function testUpdateGenreInvalidCategories()
    {
        $categories = [999, 888];
        $genre = GenreModel::factory()->create();

        $data = [
            'name' => 'Test Category',
            'categories_id' => $categories,
        ];

        $response = $this->putJson("$this->endpoint/{$genre->id}", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'categories_id',
            ]
        ]);

        $response->assertJsonFragment([0 => 'The selected categories id is invalid.']);
    }

    public function testUpdateGenreNotFound()
    {
        $categories = CategoryModel::factory()->count(2)->create()->pluck('id')->toArray();
        $data = [
            'name' => 'Updated Genre',
            'categories_id' => $categories,
        ];

        $response = $this->putJson("$this->endpoint/fake_value", $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDestroyGenreNotFound()
    {
        $response = $this->deleteJson("$this->endpoint/fake_value");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDestroyGenre()
    {
        $genre = GenreModel::factory()->create();

        $response = $this->deleteJson("$this->endpoint/{$genre->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('genres', [
            'id' => $genre->id
        ]);
    }
}
