<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Models\CastMember as ModelCastMember;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\Application\DTO\Input\CastMember\CastMemberCreateInputDto;
use Core\Application\UseCase\CastMember\CreateCastMemberUseCase;
use Core\Domain\Enum\CastMemberType;
use Tests\TestCase;

class CreateCastMemberUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $useCase = new CreateCastMemberUseCase(new CastMemberRepositoryEloquent(new ModelCastMember()));
        $response = $useCase->execute(new CastMemberCreateInputDto(
            name: 'Test',
            type: CastMemberType::ACTOR,
            isActive: true,
        ));


        $this->assertEquals('Test', $response->name);
        $this->assertEquals(CastMemberType::ACTOR, $response->type);
        $this->assertEquals(true, $response->is_active);
        $this->assertNotEmpty($response->id);

        $this->assertDatabaseHas('cast_members', [
            'id' => $response->id,
            'name' => $response->name,
            'type' => $response->type,
            'is_active' => $response->is_active,
        ]);
    }
}
