<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Application\UseCase\Category\DeleteCategoryUseCase;
use Tests\TestCase;
use App\Models\Category as CategoryModel;
use Core\Application\DTO\Input\Category\DeleteCategoryInputDto;

class DeleteCategoryUseCaseTest extends TestCase
{
    public function testExecute()
    {
        $category = CategoryModel::factory()->create();
        $useCase = new DeleteCategoryUseCase(new CategoryRepositoryEloquent(new CategoryModel()));
        $response = $useCase->execute(new DeleteCategoryInputDto(
            id: $category->id,
        ));
        $this->assertTrue($response->success);
        $this->assertSoftDeleted($category);
    }
}
