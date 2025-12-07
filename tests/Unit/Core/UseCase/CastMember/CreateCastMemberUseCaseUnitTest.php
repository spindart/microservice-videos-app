<?php

namespace Tests\Unit\Core\UseCase\CastMember;

use Core\Domain\Entity\CastMember as EntityCastMember;
use Core\Application\DTO\Input\CastMember\CastMemberCreateInputDto;
use Core\Application\DTO\Output\CastMember\CastMemberCreateOutputDto;
use Core\Application\Interfaces\DBTransactionInterface;
use Core\Application\UseCase\CastMember\CreateCastMemberUseCase;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Exception\EntityNotFoundException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateCastMemberUseCaseUnitTest extends TestCase
{

    public function testCreateNewCastMember()
    {
        $uuid = (string) Uuid::uuid4()->toString();

        $mockEntity = $this->mockEntity($uuid);
        $mockRepository = $this->mockRepository($mockEntity);

        $useCase = new CreateCastMemberUseCase($mockRepository);
        $mockInputDto = Mockery::mock(CastMemberCreateInputDto::class, ['Test Cast Member', CastMemberType::ACTOR, true]);

        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(CastMemberCreateOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);
        $this->assertEquals('Test Cast Member', $responseUseCase->name);
        $this->assertTrue(true);

        /**
         * Spies
         */

        $spy = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $spy->shouldReceive('insert')->andReturn($mockEntity);
        $useCase = new CreateCastMemberUseCase($spy);
        $mockInputDto = new CastMemberCreateInputDto(name: 'Test Cast Member', type: CastMemberType::ACTOR, isActive: true);
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('insert');
    }

    private function mockEntity(string $uuid): EntityCastMember
    {
        return
            new EntityCastMember(
                $uuid,
                'Test Cast Member',
                CastMemberType::ACTOR,
                createdAt: new \DateTime(date('Y-m-d H:i:s')),
                isActive: true
            );
    }

    private function mockRepository(
        EntityCastMember $mockEntity,
    ): CastMemberRepositoryInterface {
        $mockRepository = Mockery::mock(CastMemberRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('insert')
            ->once()
            ->andReturn($mockEntity);

        return $mockRepository;
    }

    private function mockTransaction(): DBTransactionInterface
    {
        $mockTransaction = Mockery::mock(DBTransactionInterface::class);
        $mockTransaction
            ->shouldReceive('commit')
            ->andReturn();

        $mockTransaction
            ->shouldReceive('rollback')
            ->andReturn();

        return $mockTransaction;
    }
}
