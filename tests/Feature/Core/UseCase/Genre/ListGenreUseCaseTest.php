<?php

namespace Tests\Feature\Core\UseCase\Genre;

use Tests\TestCase;
use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Application\DTO\Input\Genre\ListGenreInputDto;
use Core\Application\UseCase\Genre\ListGenreUseCase;

class ListGenreUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $category = GenreModel::factory()->create();
        $useCase = new ListGenreUseCase(new GenreRepositoryEloquent(new GenreModel()));
        $response = $useCase->execute(new ListGenreInputDto(
            id: $category->id,
        ));
        $this->assertEquals($category->name, $response->name);
        $this->assertEquals($category->created_at, $response->created_at);
        $this->assertDatabaseHas('genres', [
            'id' => $category->id,
            'name' => $category->name,
            'created_at' => $category->created_at,
        ]);
    }
}
