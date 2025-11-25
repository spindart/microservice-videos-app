<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Application\UseCase\Genre\DeleteGenreUseCase;
use Tests\TestCase;
use App\Models\Genre as GenreModel;
use Core\Application\DTO\Input\Genre\DeleteGenreInputDto;

class DeleteGenreUseCaseTest extends TestCase
{
    public function testExecute()
    {
        $genre = GenreModel::factory()->create();
        $useCase = new DeleteGenreUseCase(new GenreRepositoryEloquent(new GenreModel()));
        $response = $useCase->execute(new DeleteGenreInputDto(
            id: $genre->id,
        ));
        $this->assertTrue($response->success);
        $this->assertSoftDeleted('genres', [
            'id' => $genre->id]);
    }
}
