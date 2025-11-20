<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as ModelCategory;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Application\DTO\Input\Category\CategoryCreateInputDto;
use Core\Application\UseCase\Category\CreateCategoryUseCase;
use Tests\TestCase;

class CreateCategoryUseCaseTest extends TestCase
{

    public function testExecute()
    {
        $useCase = new CreateCategoryUseCase(new CategoryRepositoryEloquent(new ModelCategory()));
        $response = $useCase->execute(new CategoryCreateInputDto(
            name: 'Test',
            description: 'Test',
            isActive: true,
        ));


        $this->assertEquals('Test', $response->name);
        $this->assertEquals('Test', $response->description);
        $this->assertEquals(true, $response->is_active);
        $this->assertNotEmpty($response->id);

        $this->assertDatabaseHas('categories', [
            'id' => $response->id,
            'name' => $response->name,
            'description' => $response->description,
            'is_active' => $response->is_active,
        ]);
    }
}
