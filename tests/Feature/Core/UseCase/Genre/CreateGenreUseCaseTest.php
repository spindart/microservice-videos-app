<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as ModelGenre;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use App\Repositories\Transaction\DBTransaction;
use Core\Application\DTO\Input\Genre\GenreCreateInputDto;
use Core\Application\UseCase\Genre\CreateGenreUseCase;
use Core\Domain\Exception\EntityNotFoundException;
use PhpParser\Node\Stmt\Foreach_;
use Tests\TestCase;

class CreateGenreUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $useCase = new CreateGenreUseCase(
            new GenreRepositoryEloquent(new ModelGenre()),
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );
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

    public function testExecuteWithCategories()
    {
        $categories = CategoryModel::factory()->count(2)->create()->pluck('id')->toArray();

        $useCase = new CreateGenreUseCase(
            new GenreRepositoryEloquent(new ModelGenre()),
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );
        $response = $useCase->execute(new GenreCreateInputDto(
            name: 'Test',
            isActive: true,
            categoriesId: $categories,
        ));

        $this->assertEquals('Test', $response->name);
        $this->assertEquals(true, $response->is_active);
        $this->assertNotEmpty($response->id);

        $this->assertDatabaseHas('genres', [
            'id' => $response->id,
            'name' => $response->name,
            'is_active' => $response->is_active,
        ]);

        $this->assertDatabaseCount('category_genre', count($categories));

        foreach ($categories as $categoryId) {
            $this->assertDatabaseHas('category_genre', [
                'genre_id' => $response->id,
                'category_id' => $categoryId,
            ]);
        }
    }

    public function testExecuteWithInvalidCategories()
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Some categories could not be found: 999, 1000');

        $categories = CategoryModel::factory()->count(2)->create()->pluck('id')->toArray();
        $invalidCategories = [999, 1000];
        $allCategories = array_merge($categories, $invalidCategories);

        $useCase = new CreateGenreUseCase(
            new GenreRepositoryEloquent(new ModelGenre()),
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );
        $useCase->execute(new GenreCreateInputDto(
            name: 'Test',
            isActive: true,
            categoriesId: $allCategories,
        ));
    }

    public function testExecuteTransactionRollbackOnError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Simulated failure');

        $mockRepository = $this->createMock(GenreRepositoryEloquent::class);
        $mockRepository->method('insert')->will($this->throwException(new \Exception('Simulated failure')));

        $useCase = new CreateGenreUseCase(
            $mockRepository,
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );

        try {
            $useCase->execute(new GenreCreateInputDto(
                name: 'Test',
                isActive: true,
            ));
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('genres', [
                'name' => 'Test',
            ]);
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
            throw $e;
        }
    }
}
