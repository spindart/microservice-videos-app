<?php

namespace Tests\Unit\Core\UseCase\Genre;

use Core\Application\DTO\Input\Genre\DeleteGenreInputDto;
use Core\Application\DTO\Output\Genre\DeleteGenreOutputDto;
use Core\Application\UseCase\Genre\DeleteGenreUseCase;
use Core\Domain\Repository\GenreRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteGenreUseCaseUnitTest extends TestCase
{
    public function testDeleteGenre()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->once()->andReturn(true);
        $useCase = new DeleteGenreUseCase($mockRepository);
        $deleteGenreInputDto = Mockery::mock(DeleteGenreInputDto::class, [$id]);
        $responseUseCase = $useCase->execute($deleteGenreInputDto);
        $this->assertInstanceOf(DeleteGenreOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);
        $useCase = new DeleteGenreUseCase($spy);
        $deleteGenreInputDto = new DeleteGenreInputDto(id: $id);
        $responseUseCase = $useCase->execute($deleteGenreInputDto);
        $spy->shouldHaveReceived('delete');
        Mockery::close();
    }

    public function testDeleteFalse()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(false);
        $useCase = new DeleteGenreUseCase($mockRepository);
        $deleteGenreInputDto = Mockery::mock(DeleteGenreInputDto::class, [$id]);
        $responseUseCase = $useCase->execute($deleteGenreInputDto);
        $this->assertInstanceOf(DeleteGenreOutputDto::class, $responseUseCase);
        $this->assertFalse($responseUseCase->success);
    }

    protected function tearDown(): void
    {

        Mockery::close();
        parent::tearDown();
    }
}
