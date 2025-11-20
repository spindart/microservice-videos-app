<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Genre as GenreModel;
use Illuminate\Http\Response;

class GenreApiTest extends TestCase
{
    protected $endpoint = '/api/genres';

    public function testListEmptyGenres()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function testListGenres()
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
    }

    public function testListPaginateGenres()
    {
        GenreModel::factory()->count(30)->create();

        $response = $this->getJson("$this->endpoint?page=2");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, $response['meta']['current_page']);
        $response->assertJsonCount(10, 'data');
    }

    public function testListGenreNotFound()
    {
        $response = $this->getJson("$this->endpoint/fake_value");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListGenre()
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
            'name' => 'New Genre 2',
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

        $data3 = [
            'name' => 'New Genre 3',
            'is_active' => false,
        ];

        $response = $this->postJson($this->endpoint, $data3);
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
        $this->assertEquals($data3['name'], $response['data']['name']);
        $this->assertFalse($response['data']['is_active']);
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

    public function testUpdateGenreInvalidData()
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

    public function testUpdateGenreNotFound()
    {
        $data = [
            'name' => 'Updated Genre',
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
