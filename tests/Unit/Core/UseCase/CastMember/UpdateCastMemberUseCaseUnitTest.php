<?php

namespace Tests\Unit\Core\UseCase\CastMember;

use Core\Application\DTO\Input\CastMember\CastMemberUpdateInputDto;
use Core\Application\DTO\Output\CastMember\CastMemberUpdateOutputDto;
use Core\Application\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use DateTime;
use Mockery;
use Ramsey\Uuid\Nonstandard\Uuid;
use stdClass;
use Tests\TestCase;

class UpdateCastMemberUseCaseUnitTest extends TestCase
{

    public function testUpdateCastMember()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockEntity = new CastMemberEntity($id, 'Cast Member Test', CastMemberType::ACTOR, new DateTime(), true);
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->once()->with($id)->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->once()->andReturn($mockEntity);
        $useCase = new UpdateCastMemberUseCase($mockRepository);
        $castMemberInputDto =  Mockery::mock(CastMemberUpdateInputDto::class, [$id, 'Cast Member Updated', CastMemberType::DIRECTOR, false]);

        $reponseUsecase = $useCase->execute($castMemberInputDto);
        $this->assertInstanceOf(CastMemberUpdateOutputDto::class, $reponseUsecase);
        $this->assertEquals($id, $reponseUsecase->id);
        $this->assertEquals('Cast Member Updated', $reponseUsecase->name);
        $this->assertEquals(CastMemberType::DIRECTOR, $reponseUsecase->type);
        $this->assertFalse($reponseUsecase->is_active);

          /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $spy->shouldReceive('findById')->andReturn($mockEntity);
        $spy->shouldReceive('createdAt')->andReturn($mockEntity->createdAt());

        $spy->shouldReceive('update')->andReturn($mockEntity);
        $useCase = new UpdateCastMemberUseCase($spy);
        $castMemberInputDto =  Mockery::mock(CastMemberUpdateInputDto::class, [$id, 'Cast Member Updated', CastMemberType::DIRECTOR, false]);
        $responseUseCase = $useCase->execute($castMemberInputDto);
        $spy->shouldHaveReceived('findById');
        $spy->shouldHaveReceived('update');


        Mockery::close();
    }
}
