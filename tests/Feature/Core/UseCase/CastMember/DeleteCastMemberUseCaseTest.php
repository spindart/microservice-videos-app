<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\Application\UseCase\CastMember\DeleteCastMemberUseCase;
use Tests\TestCase;
use App\Models\CastMember as CastMemberModel;
use Core\Application\DTO\Input\CastMember\DeleteCastMemberInputDto;

class DeleteCastMemberUseCaseTest extends TestCase
{
    public function testExecute()
    {
        $castMembers = CastMemberModel::factory()->create();
        $useCase = new DeleteCastMemberUseCase(new CastMemberRepositoryEloquent(new CastMemberModel()));
        $response = $useCase->execute(new DeleteCastMemberInputDto(
            id: $castMembers->id,
        ));
        $this->assertTrue($response->success);
        $this->assertSoftDeleted($castMembers);
    }
}
