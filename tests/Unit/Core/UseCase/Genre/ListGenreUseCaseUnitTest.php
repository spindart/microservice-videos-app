<?php

namespace Tests\Unit\Core\UseCase\Genre;

use Core\Application\DTO\Input\Genre\ListGenreInputDto;
use Core\Application\DTO\Output\Genre\ListGenreOutputDto;
use Core\Application\UseCase\Genre\ListGenreUseCase;
use Core\Domain\Entity\Genre;
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
        $mockEntity = new Genre(
            id: $id,
            name: 'Test Genre',
            isActive: true
        );
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->with($id)->andReturn($mockEntity);
        $mockRepository->shouldReceive('createdAt')->andReturn($mockEntity->createdAt());
        $mockRepository->shouldReceive('id')->andReturn($id);
        $useCase = new ListGenreUseCase($mockRepository);
        $listGenreInputDto = new ListGenreInputDto(id: $id);
        $responseUseCase = $useCase->execute($listGenreInputDto);

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
        $ListGenreInputDto = new ListGenreInputDto(id: $id);
        $responseUseCase = $useCase->execute($ListGenreInputDto);
        $spy->shouldHaveReceived('findById');
        Mockery::close();
    }
}
