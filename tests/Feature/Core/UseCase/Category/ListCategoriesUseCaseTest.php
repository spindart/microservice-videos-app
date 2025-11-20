<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Application\DTO\Input\Category\ListCategoriesInputDto;
use Core\Application\UseCase\Category\ListCategoriesUseCase;
use Tests\TestCase;

class ListCategoriesUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $categories = CategoryModel::factory()->count(10)->create();
        $useCase = new ListCategoriesUseCase(new CategoryRepositoryEloquent(new CategoryModel()));
        $response = $useCase->execute(new ListCategoriesInputDto());
        $this->assertCount(10, $response->items);
        $this->assertEquals(10, $response->total);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(10, $response->per_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(10, $response->from);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(1, $response->first_page);

        $this->assertDatabaseHas('categories', [
            'id' => $categories->first()->id,
            'name' => $categories->first()->name,
            'description' => $categories->first()->description,
            'created_at' => $categories->first()->created_at,
            'updated_at' => $categories->first()->updated_at,
        ]);
    }

    public function testExecuteWithNoResults()
    {
        $useCase = new ListCategoriesUseCase(new CategoryRepositoryEloquent(new CategoryModel()));
        $response = $useCase->execute(new ListCategoriesInputDto());
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
