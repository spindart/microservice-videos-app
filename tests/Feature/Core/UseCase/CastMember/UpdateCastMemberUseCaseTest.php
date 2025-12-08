<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use Tests\TestCase;
use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\Application\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\Application\DTO\Input\CastMember\CastMemberUpdateInputDto;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityNotFoundException;

class UpdateCastMemberUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $category = CastMemberModel::factory()->create();
        $useCase = new UpdateCastMemberUseCase(new CastMemberRepositoryEloquent(new CastMemberModel()));
        $response = $useCase->execute(new CastMemberUpdateInputDto(
            id: $category->id,
            name: 'Test',
            type: CastMemberType::ACTOR,
        ));
        $this->assertEquals('Test', $response->name);
        $this->assertEquals(CastMemberType::ACTOR, $response->type);
        $this->assertDatabaseHas('cast_members', [
            'id' => $category->id,
            'name' => $response->name,
            'type' => $response->type,
        ]);
    }

    public function testExecuteWithNoId()
    {
        try {
            $useCase = new UpdateCastMemberUseCase(new CastMemberRepositoryEloquent(new CastMemberModel()));
            $response = $useCase->execute(new CastMemberUpdateInputDto(
                id: '0',
                name: 'Test',
                type: CastMemberType::ACTOR,
            ));
            $this->assertTrue(false);
        } catch (EntityNotFoundException $e) {
            $this->assertInstanceOf(EntityNotFoundException::class, $e);
        }
    }
}
