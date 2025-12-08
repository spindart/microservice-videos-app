<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use cORE\Application\DTO\Input\CastMember\ListCastMembersInputDto;
use Core\Application\UseCase\CastMember\ListCastMembersUseCase;
use Tests\TestCase;

class ListCastMembersUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $castMembers = CastMemberModel::factory()->count(10)->create();
        $useCase = new ListCastMembersUseCase(new CastMemberRepositoryEloquent(new CastMemberModel()));
        $response = $useCase->execute(new ListCastMembersInputDto());
        $this->assertCount(10, $response->items);
        $this->assertEquals(10, $response->total);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(10, $response->per_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(10, $response->from);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(1, $response->first_page);

        $this->assertDatabaseHas('cast_members', [
            'id' => $castMembers->first()->id,
            'name' => $castMembers->first()->name,
            'type' => $castMembers->first()->type,
            'created_at' => $castMembers->first()->created_at,
            'updated_at' => $castMembers->first()->updated_at,
        ]);
    }

    public function testExecuteWithNoResults()
    {
        $useCase = new ListCastMembersUseCase(new CastMemberRepositoryEloquent(new CastMemberModel()));
        $response = $useCase->execute(new ListCastMembersInputDto());
        $this->assertCount(0, $response->items);
        $this->assertEquals(0, $response->total);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(10, $response->per_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(0, $response->from);
        $this->assertEquals(0, $response->to);
        $this->assertEquals(0, $response->first_page);
    }
}
