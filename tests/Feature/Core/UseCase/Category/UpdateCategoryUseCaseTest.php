<?php

namespace Tests\Feature\Core\UseCase\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Application\UseCase\Category\UpdateCategoryUseCase;
use Core\Application\DTO\Input\Category\CategoryUpdateInputDto;
use Core\Domain\Exception\EntityNotFoundException;

class UpdateCategoryUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $category = CategoryModel::factory()->create();
        $useCase = new UpdateCategoryUseCase(new CategoryRepositoryEloquent(new CategoryModel()));
        $response = $useCase->execute(new CategoryUpdateInputDto(
            id: $category->id,
            name: 'Test',
            description: 'Test',
        ));
        $this->assertEquals('Test', $response->name);
        $this->assertEquals('Test', $response->description);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $response->name,
            'description' => $response->description,
        ]);
    }

    public function testExecuteWithNoId()
    {
        try {
            $useCase = new UpdateCategoryUseCase(new CategoryRepositoryEloquent(new CategoryModel()));
            $response = $useCase->execute(new CategoryUpdateInputDto(
                id: '0',
                name: 'Test',
                description: 'Test',
            ));
            $this->assertTrue(false);
        } catch (EntityNotFoundException $e) {
            $this->assertInstanceOf(EntityNotFoundException::class, $e);
        }
    }
}
