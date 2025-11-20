<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Application\DTO\Input\Genre\ListGenresInputDto;
use Core\Application\UseCase\Genre\ListGenresUseCase;
use Tests\TestCase;

class ListGenresUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $genres = GenreModel::factory()->count(10)->create();
        $useCase = new ListGenresUseCase(new GenreRepositoryEloquent(new GenreModel()));
        $response = $useCase->execute(new ListGenresInputDto());
        $this->assertCount(10, $response->items);
        $this->assertEquals(10, $response->total);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(10, $response->per_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(10, $response->from);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(1, $response->first_page);

        $this->assertDatabaseHas('Genres', [
            'id' => $genres->first()->id,
            'name' => $genres->first()->name,
            'created_at' => $genres->first()->created_at,
            'updated_at' => $genres->first()->updated_at,
        ]);
    }

    public function testExecuteWithNoResults()
    {
        $useCase = new ListGenresUseCase(new GenreRepositoryEloquent(new GenreModel()));
        $response = $useCase->execute(new ListGenresInputDto());
        $this->assertCount(0, $response->items);
        $this->assertEquals(0, $response->total);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(10, $response->per_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(0, $response->from);
        $this->assertEquals(0, $response->to);
        $this->assertEquals(0, $response->first_page);
    }
}
