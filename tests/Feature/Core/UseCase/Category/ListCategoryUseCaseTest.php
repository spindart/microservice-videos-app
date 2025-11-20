<?php

namespace Tests\Feature\Core\UseCase\Category;

use Tests\TestCase;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Application\DTO\Input\Category\ListCategoryInputDto;
use Core\Application\UseCase\Category\ListCategoryUseCase;

class ListCategoryUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $category = CategoryModel::factory()->create();
        $useCase = new ListCategoryUseCase(new CategoryRepositoryEloquent(new CategoryModel()));
        $response = $useCase->execute(new ListCategoryInputDto(
            id: $category->id,
        ));
        $this->assertEquals($category->name, $response->name);
        $this->assertEquals($category->description, $response->description);
        $this->assertEquals($category->created_at, $response->created_at);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'created_at' => $category->created_at,
        ]);
    }
}
