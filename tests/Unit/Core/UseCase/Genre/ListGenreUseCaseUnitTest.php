<?php

namespace Tests\Unit\Core\UseCase\Genre;

use Core\Application\DTO\Input\Genre\ListGenreInputDto;
use Core\Application\DTO\Output\Genre\ListGenreOutputDto;
use Core\Application\UseCase\Genre\ListGenreUseCase;
use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use stdClass;

class ListGenreUseCaseUnitTest extends TestCase
{
    public function testGetById()
    {
        $id = (string) Uuid::uuid4()->toString();
        // $mockEntity = new EntityGenre(
        //     id: $id,
        //     name: 'Test Genre',
        //     isActive: true
        // );
        $mockEntity = Mockery::mock(EntityGenre::class, [
            $id,
            'Test Genre',
            true,
            [],
            new \DateTime(),
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('id')->andReturn($id);
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->with($id)->once()->andReturn($mockEntity);
        $mockRepository->shouldReceive('createdAt')->andReturn($mockEntity->createdAt());
        $mockRepository->shouldReceive('id')->andReturn($id);
        $useCase = new ListGenreUseCase($mockRepository);
        $mockInputDto = Mockery::mock(ListGenreInputDto::class, [$id]);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertTrue(true);
        $this->assertInstanceOf(ListGenreOutputDto::class, $responseUseCase);
        $this->assertEquals($id, $responseUseCase->id);
        $this->assertEquals('Test Genre', $responseUseCase->name);
        $this->assertTrue($responseUseCase->is_active);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive('findById')->with($id)->andReturn($mockEntity);
        $useCase = new ListGenreUseCase($spy);
        $mockInputDto = Mockery::mock(ListGenreInputDto::class, [$id]);
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('findById');
        Mockery::close();
    }
}
