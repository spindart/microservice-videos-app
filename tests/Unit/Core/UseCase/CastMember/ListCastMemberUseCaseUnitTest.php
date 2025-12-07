<?php

namespace Tests\Unit\Core\UseCase\CastMember;

use Core\Application\DTO\Input\CastMember\ListCastMemberInputDto;
use Core\Application\DTO\Output\CastMember\ListCastMemberOutputDto;
use Core\Application\UseCase\CastMember\ListCastMemberUseCase;
use Core\Domain\Entity\CastMember as EntityCastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class ListCastMemberUseCaseUnitTest extends TestCase
{

    public function testGetById()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockEntity = new EntityCastMember($id, "Test Cast Member", CastMemberType::ACTOR, new \DateTime(), true);
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->with($id)->once()->andReturn($mockEntity);
        $useCase = new ListCastMemberUseCase($mockRepository);
        $mockInputDto = Mockery::mock(ListCastMemberInputDto::class, [$id]);
        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertEquals($id, $responseUseCase->id);
        $this->assertEquals("Test Cast Member", $responseUseCase->name);
        $this->assertInstanceOf(ListCastMemberOutputDto::class, $responseUseCase);


        /**
         * Spies
         */

        $spy = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $spy->shouldReceive('findById')->with($id)->andReturn($mockEntity);
        $useCase = new ListCastMemberUseCase($spy);
        $mockInputDto = Mockery::mock(ListCastMemberInputDto::class, [$id]);
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('findById');
    }



    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
