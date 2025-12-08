<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use Tests\TestCase;
use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\Application\DTO\Input\CastMember\ListCastMemberInputDto;
use Core\Application\UseCase\CastMember\ListCastMemberUseCase;

class ListCastMemberUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $castMembers = CastMemberModel::factory()->create();
        $useCase = new ListCastMemberUseCase(new CastMemberRepositoryEloquent(new CastMemberModel()));
        $response = $useCase->execute(new ListCastMemberInputDto(
            id: $castMembers->id,
        ));
        $this->assertEquals($castMembers->name, $response->name);
        $this->assertEquals($castMembers->created_at, $response->created_at);
        $this->assertDatabaseHas('cast_members', [
            'id' => $castMembers->id,
            'name' => $castMembers->name,
            'type' => $castMembers->type,
            'created_at' => $castMembers->created_at,
        ]);
    }
}
