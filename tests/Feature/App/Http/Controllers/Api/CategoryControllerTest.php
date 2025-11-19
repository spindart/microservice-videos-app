<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Requests\{StoreCategoryRequest, UpdateCategoryRequest};
use App\Http\Controllers\Api\CategoryController;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Application\UseCase\Category\CreateCategoryUseCase;
use Core\Application\UseCase\Category\DeleteCategoryUseCase;
use Core\Application\UseCase\Category\ListCategoriesUseCase;
use Core\Application\UseCase\Category\ListCategoryUseCase;
use Core\Application\UseCase\Category\UpdateCategoryUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    protected $repository;
    protected $controller;

    protected function setUp(): void
    {
        $this->repository = new CategoryRepositoryEloquent(
            new CategoryModel()
        );

        $this->controller = new CategoryController();
        parent::setUp();
    }

    public function testIndex()
    {
        $useCase = new ListCategoriesUseCase($this->repository);

        $response = $this->controller->index(new Request(), $useCase);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $useCase = new CreateCategoryUseCase($this->repository);

        $request = new StoreCategoryRequest();
        $request->headers->set('content-type', 'application/json');

        $request->setJson(new ParameterBag([
            'name' => 'Test',
            'description' => 'Test Description'
        ]));


        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    public function testShow()
    {
        $category = CategoryModel::factory()->create();
        $useCase = new ListCategoryUseCase($this->repository);
        $response = $this->controller->show(
            useCase: $useCase,
            id: $category->id,
        );
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate()
    {
        $category = CategoryModel::factory()->create();
        $useCase = new UpdateCategoryUseCase($this->repository);

        $request = new UpdateCategoryRequest();
        $request->headers->set('content-type', 'application/json');

        $request->setJson(new ParameterBag([
            'name' => 'Name Updated',
            'description' => 'Description Updated'
        ]));

        $response = $this->controller->update(request: $request, useCase: $useCase, id: $category->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas(
            'categories',
            [
                'id' => $category->id,
                'name' => 'Name Updated',
                'description' => 'Description Updated'
            ]
        );
    }

    public function testDelete()
    {
        $category = CategoryModel::factory()->create();
        $useCase = new DeleteCategoryUseCase($this->repository);

        $response = $this->controller->destroy(useCase: $useCase, id: $category->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
    }
}
