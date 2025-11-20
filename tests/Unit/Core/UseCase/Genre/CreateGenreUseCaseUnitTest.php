<?php

namespace Tests\Unit\Core\UseCase\Genre;

use Core\Domain\Entity\Genre as EntityGenre;
use Core\Application\DTO\Input\Genre\GenreCreateInputDto;
use Core\Application\DTO\Output\Genre\GenreCreateOutputDto;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Application\UseCase\Genre\CreateGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateGenreUseCaseUnitTest extends TestCase
{

    public function testCreateNewGenre()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $mockEntity = new EntityGenre(
            id: $uuid,
            name: 'Test Genre',
            isActive: true
        );
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('id')->andReturn($uuid);
        $mockRepository->shouldReceive('insert')->andReturn($mockEntity);
        $useCase = new CreateGenreUseCase($mockRepository);
        $genreCreateInputDto = new GenreCreateInputDto(
            name: 'Test Genre',
            isActive: true
        );
        $responseUseCase = $useCase->execute($genreCreateInputDto);
        $this->assertInstanceOf(GenreCreateOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);
        $this->assertEquals('Test Genre', $responseUseCase->name);
        $this->assertTrue($responseUseCase->is_active);
        $this->assertTrue(true);
        
        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive('insert')->andReturn($mockEntity);
        $spy->shouldReceive('createdAt')->andReturn($mockEntity->createdAt());
        $useCase = new CreateGenreUseCase($spy);
        $genreCreateInputDto = new GenreCreateInputDto(
            name: 'Test Genre',
            isActive: true
        );
        $responseUseCase = $useCase->execute($genreCreateInputDto);
        $spy->shouldHaveReceived('insert');
        Mockery::close();
    }
}
