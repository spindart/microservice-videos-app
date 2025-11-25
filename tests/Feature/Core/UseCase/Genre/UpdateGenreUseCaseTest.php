<?php

namespace Tests\Feature\Core\UseCase\Genre;

use Tests\TestCase;
use App\Models\Genre as GenreModel;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use App\Repositories\Transaction\DBTransaction;
use Core\Application\UseCase\Genre\UpdateGenreUseCase;
use Core\Application\DTO\Input\Genre\GenreUpdateInputDto;
use Core\Domain\Exception\EntityNotFoundException;

class UpdateGenreUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $category = GenreModel::factory()->create();
        $useCase = new UpdateGenreUseCase(
            new GenreRepositoryEloquent(new GenreModel()),
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );
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

    public function testExecuteWithCategories()
    {
        $categories = CategoryModel::factory()->count(2)->create()->pluck('id')->toArray();
        $genre = GenreModel::factory()->create();
        $useCase = new UpdateGenreUseCase(
            new GenreRepositoryEloquent(new GenreModel()),
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );
        $response = $useCase->execute(new GenreUpdateInputDto(
            id: $genre->id,
            name: 'Test',
            categoriesId: $categories,
        ));
        $this->assertEquals('Test', $response->name);
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => $response->name,
        ]);

        foreach ($categories as $categoryId) {
            $this->assertDatabaseHas('category_genre', [
                'genre_id' => $genre->id,
                'category_id' => $categoryId,
            ]);
        }

        $categories2 = CategoryModel::factory()->count(2)->create()->pluck('id')->toArray();
        $categories = array_merge($categories, $categories2);

        $response = $useCase->execute(new GenreUpdateInputDto(
            id: $genre->id,
            name: 'Test',
            categoriesId: $categories
        ));

        foreach ($categories as $categoryId) {
            $this->assertDatabaseHas('category_genre', [
                'genre_id' => $genre->id,
                'category_id' => $categoryId,
            ]);
        }
        $this->assertDatabaseCount('category_genre', 4);
    }

    public function testExecuteWithNoId()
    {
        try {
            $useCase = new UpdateGenreUseCase(
                new GenreRepositoryEloquent(new GenreModel()),
                new CategoryRepositoryEloquent(
                    new CategoryModel()
                ),
                new DBTransaction()
            );
            $useCase->execute(new GenreUpdateInputDto(
                id: '0',
                name: 'Test',
            ));
            $this->assertTrue(false);
        } catch (EntityNotFoundException $e) {
            $this->assertInstanceOf(EntityNotFoundException::class, $e);
        }
    }

    public function testExecuteTransactionRollbackOnError()
    {
        $genre = GenreModel::factory()->create();
        $useCase = new UpdateGenreUseCase(
            new GenreRepositoryEloquent(new GenreModel()),
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );

        $this->expectException(EntityNotFoundException::class);
        $useCase->execute(new GenreUpdateInputDto(
            id: $genre->id,
            name: 'Test',
            categoriesId: ['non-existing-id1', 'non-existing-id2'],
        ));

    }
}
