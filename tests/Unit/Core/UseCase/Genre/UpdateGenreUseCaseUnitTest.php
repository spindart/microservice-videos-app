<?php

namespace Tests\Unit\Core\UseCase\Genre;

use Core\Application\DTO\Input\Genre\GenreUpdateInputDto;
use Core\Application\DTO\Output\Genre\GenreUpdateOutputDto;
use Core\Application\UseCase\Genre\UpdateGenreUseCase;
use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateGenreUseCaseUnitTest extends TestCase
{
    public function testUpdateGenre()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockEntity = new EntityGenre(
            id: $id,
            name: 'Test Genre',
            isActive: true
        );

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->andReturn($mockEntity);
        $useCase = new UpdateGenreUseCase($mockRepository);
        $genreUpdateInputDto = new GenreUpdateInputDto(
            id: $id,
            name: 'Test Genre Updated',
            isActive: true
        );
        $responseUseCase = $useCase->execute($genreUpdateInputDto);
        $this->assertInstanceOf(GenreUpdateOutputDto::class, $responseUseCase);
        $this->assertEquals($id, $responseUseCase->id);
        $this->assertEquals('Test Genre Updated', $responseUseCase->name);
        $this->assertTrue($responseUseCase->is_active);
        $this->assertTrue(true);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive('findById')->andReturn($mockEntity);
        $spy->shouldReceive('createdAt')->andReturn($mockEntity->createdAt());

        $spy->shouldReceive('update')->andReturn($mockEntity);
        $useCase = new UpdateGenreUseCase($spy);
        $genreUpdateInputDto = new GenreUpdateInputDto(
            id: $id,
            name: 'Test Genre Updated',
            isActive: true
        );
        $responseUseCase = $useCase->execute($genreUpdateInputDto);
        $spy->shouldHaveReceived('findById');
        $spy->shouldHaveReceived('update');


        Mockery::close();
    }
}
