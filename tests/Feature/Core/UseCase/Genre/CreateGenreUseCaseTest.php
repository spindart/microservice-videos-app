<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as ModelGenre;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Application\DTO\Input\Genre\GenreCreateInputDto;
use Core\Application\UseCase\Genre\CreateGenreUseCase;
use Tests\TestCase;

class CreateGenreUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $useCase = new CreateGenreUseCase(new GenreRepositoryEloquent(new ModelGenre()));
        $response = $useCase->execute(new GenreCreateInputDto(
            name: 'Test',
            isActive: true,
        ));

        $this->assertEquals('Test', $response->name);
        $this->assertEquals(true, $response->is_active);
        $this->assertNotEmpty($response->id);

        $this->assertDatabaseHas('genres', [
            'id' => $response->id,
            'name' => $response->name,
            'is_active' => $response->is_active,
        ]);
    }
}
