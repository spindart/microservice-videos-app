<?php

namespace Tests\Feature\Core\UseCase\Genre;

use Tests\TestCase;
use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Application\UseCase\Genre\UpdateGenreUseCase;
use Core\Application\DTO\Input\Genre\GenreUpdateInputDto;
use Core\Domain\Exception\EntityNotFoundException;

class UpdateGenreUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $category = GenreModel::factory()->create();
        $useCase = new UpdateGenreUseCase(new GenreRepositoryEloquent(new GenreModel()));
        $response = $useCase->execute(new GenreUpdateInputDto(
            id: $category->id,
            name: 'Test',
        ));
        $this->assertEquals('Test', $response->name);
        $this->assertDatabaseHas('genres', [
            'id' => $category->id,
            'name' => $response->name,
        ]);
    }

    public function testExecuteWithNoId()
    {
        try {
            $useCase = new UpdateGenreUseCase(new GenreRepositoryEloquent(new GenreModel()));
            $useCase->execute(new GenreUpdateInputDto(
                id: '0',
                name: 'Test',
            ));
            $this->assertTrue(false);
        } catch (EntityNotFoundException $e) {
            $this->assertInstanceOf(EntityNotFoundException::class, $e);
        }
    }
}
