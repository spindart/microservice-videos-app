<?php

namespace Test\Unit\Core\UseCase\CastMember;

use Core\Application\DTO\Input\CastMember\DeleteCastMemberInputDto;
use Core\Application\DTO\Output\CastMember\DeleteCastMemberOutputDto;
use Core\Application\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Mockery;
use Ramsey\Uuid\Nonstandard\Uuid;
use stdClass;
use Tests\TestCase;

class DeleteCastMemberUseCaseUnitTest extends TestCase
{

    public function testDeleteCastMember()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->once()->andReturn(true);
        $useCase = new DeleteCastMemberUseCase($mockRepository);
        $deleteCastMemberInputDto = Mockery::mock(DeleteCastMemberInputDto::class, [$id]);
        $reponseUseCase = $useCase->execute($deleteCastMemberInputDto);
        $this->assertInstanceOf(DeleteCastMemberOutputDto::class, $reponseUseCase);
        $this->assertTrue($reponseUseCase->success);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $spy->shouldReceive('delete')->andReturn(true);
        $useCase = new DeleteCastMemberUseCase($spy);
        $deleteCategoryInputDto = new DeleteCastMemberInputDto(id: $id);
        $responseUseCase = $useCase->execute($deleteCategoryInputDto);
        $spy->shouldHaveReceived('delete');
        Mockery::close();
    }
}
