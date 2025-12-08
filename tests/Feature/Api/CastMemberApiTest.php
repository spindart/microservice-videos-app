<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\CastMember as CastMemberModel;
use Core\Domain\Enum\CastMemberType;
use Illuminate\Http\Response;

class CastMemberApiTest extends TestCase
{
    protected $endpoint = '/api/cast-members';

    public function testListEmptyCastMembers()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function testListCastMembers()
    {
        CastMemberModel::factory()->count(30)->create();

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

    public function testListPaginateCastMembers()
    {
        CastMemberModel::factory()->count(30)->create();

        $response = $this->getJson("$this->endpoint?page=2");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, $response['meta']['current_page']);
        $response->assertJsonCount(10, 'data');
    }

    public function testListCastMemberNotFound()
    {
        $response = $this->getJson("$this->endpoint/fake_value");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListCastMember()
    {
        $category = CastMemberModel::factory()->create();
        $response = $this->getJson("$this->endpoint/{$category->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'type',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($category->name, $response['data']['name']);
    }

    public function testStoreCastMember()
    {

        $data = [
            'name' => 'New CastMember 2',
            'type' => CastMemberType::ACTOR,
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'type',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertEquals($data['type']->value, $response['data']['type']);
    }

    public function testStoreCastMemberInvalidData()
    {
        $data = [
            'name' => 'Ne',
            'type' => str_repeat('a', 300),
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'type'
            ]
        ]);
    }

    public function testUpdateCastMember()
    {
        $category = CastMemberModel::factory()->create();

        $data = [
            'name' => 'Updated CastMember',
            'type' => CastMemberType::ACTOR,
        ];

        $response = $this->putJson("$this->endpoint/{$category->id}", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' =>
            [
                'id',
                'name',
                'type',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertEquals($data['type']->value, $response['data']['type']);

        $this->assertDatabaseHas('cast_members', [
            'id' => $category->id,
            'name' => $data['name'],
            'type' => $data['type']->value
        ]);
    }

    public function testUpdateCastMemberInvalidData()
    {
        $category = CastMemberModel::factory()->create();

        $data = [
            'name' => 'Up',
            'type' => CastMemberType::DIRECTOR,
        ];

        $response = $this->putJson("$this->endpoint/{$category->id}", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);
    }

    public function testUpdateCastMemberNotFound()
    {
        $data = [
            'name' => 'Updated CastMember',
            'type' => CastMemberType::ACTOR,
        ];

        $response = $this->putJson("$this->endpoint/fake_value", $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDestroyCastMemberNotFound()
    {
        $response = $this->deleteJson("$this->endpoint/fake_value");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDestroyCastMember()
    {
        $category = CastMemberModel::factory()->create();

        $response = $this->deleteJson("$this->endpoint/{$category->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('cast_members', [
            'id' => $category->id
        ]);
    }
}
