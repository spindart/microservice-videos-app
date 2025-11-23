<?php

namespace Tests\Unit\Core\UseCase\Genre;

use Core\Domain\Entity\Genre as EntityGenre;
use Core\Application\DTO\Input\Genre\GenreUpdateInputDto;
use Core\Application\DTO\Output\Genre\GenreUpdateOutputDto;
use Core\Application\Interfaces\DBTransactionInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Application\UseCase\Genre\UpdateGenreUseCase;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateGenreUseCaseUnitTest extends TestCase
{

    public function testUpdateNewGenre()
    {
        $uuid = (string) Uuid::uuid4()->toString();

        $mockEntity = $this->mockEntity($uuid);
        $mockRepository = $this->mockRepository($uuid, $mockEntity);
        $mockCategoryRepository = $this->mockCategoryRepository($uuid);
        $mockTransaction = $this->mockTransaction();

        $useCase = new UpdateGenreUseCase($mockRepository, $mockCategoryRepository, $mockTransaction);
        $mockInputDto = Mockery::mock(GenreUpdateInputDto::class, [$uuid, 'Test Genre', [$uuid], true]);

        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(GenreUpdateOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);
        $this->assertEquals('Test Genre', $responseUseCase->name);
        $this->assertTrue($responseUseCase->is_active);
        $this->assertTrue(true);
    }

    public function testUpdateNewGenreException()
    {
        $this->expectException(EntityNotFoundException::class);
        $uuid = (string) Uuid::uuid4()->toString();

        $mockEntity = $this->mockEntity($uuid);
        $mockRepository = $this->mockRepository($uuid, $mockEntity, 0);
        $mockCategoryRepository = $this->mockCategoryRepository($uuid);
        $mockTransaction = $this->mockTransaction();

        $useCase = new UpdateGenreUseCase($mockRepository, $mockCategoryRepository, $mockTransaction);
        $mockInputDto = Mockery::mock(GenreUpdateInputDto::class, [$uuid, 'Test Genre', [$uuid, 'fake_value', 'fake_value2'], true]);

        $useCase->execute($mockInputDto);
    }

    private function mockRepository($uuid, $mockEntity, $timesCalled = 1)
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('id')->andReturn($uuid);
        $mockRepository->shouldReceive('findById')->once()->with($uuid)->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->times($timesCalled)->andReturn($mockEntity);

        return $mockRepository;
    }

    private function mockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, DBTransactionInterface::class);
        $mockTransaction->shouldReceive('commit')->andReturnTrue();
        $mockTransaction->shouldReceive('rollback')->andReturnTrue();
        return $mockTransaction;
    }

    private function mockCategoryRepository($uuid)
    {
        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsList')->once()->andReturn([$uuid]);
        return $mockCategoryRepository;
    }
    private function mockEntity($uuid)
    {


        $mockEntity = Mockery::mock(EntityGenre::class, [
            $uuid,
            'Test Genre',
            true,
            [$uuid],
            new \DateTime(),
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('update')->once()->andReturn($mockEntity);
        $mockEntity->shouldReceive('addCategory')->andReturn($mockEntity);

        return $mockEntity;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
