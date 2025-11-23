<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Requests\Genre\{StoreGenreRequest, UpdateGenreRequest};
use App\Http\Controllers\Api\GenreController;
use App\Models\Genre as GenreModel;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use App\Repositories\Transaction\DBTransaction;
use Core\Application\UseCase\Genre\CreateGenreUseCase;
use Core\Application\UseCase\Genre\DeleteGenreUseCase;
use Core\Application\UseCase\Genre\ListGenresUseCase;
use Core\Application\UseCase\Genre\ListGenreUseCase;
use Core\Application\UseCase\Genre\UpdateGenreUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    protected $repository;
    protected $controller;

    protected function setUp(): void
    {
        $this->repository = new GenreRepositoryEloquent(
            new GenreModel()
        );

        $this->controller = new GenreController();
        parent::setUp();
    }

    public function testIndex()
    {
        $useCase = new ListGenresUseCase($this->repository);

        $response = $this->controller->index(new Request(), $useCase);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $useCase = new CreateGenreUseCase(
            $this->repository,
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );

        $request = new StoreGenreRequest();
        $request->headers->set('content-type', 'application/json');

        $request->setJson(new ParameterBag([
            'name' => 'Test',
        ]));


        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    public function testShow()
    {
        $genre = GenreModel::factory()->create();
        $useCase = new ListGenreUseCase($this->repository);
        $response = $this->controller->show(
            useCase: $useCase,
            id: $genre->id,
        );
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate()
    {
        $genre = GenreModel::factory()->create();
        $useCase = new UpdateGenreUseCase(
            $this->repository,
            new CategoryRepositoryEloquent(
                new CategoryModel()
            ),
            new DBTransaction()
        );


        $request = new UpdateGenreRequest();
        $request->headers->set('content-type', 'application/json');

        $request->setJson(new ParameterBag([
            'name' => 'Name Updated',
        ]));

        $response = $this->controller->update(request: $request, useCase: $useCase, id: $genre->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas(
            'Genres',
            [
                'id' => $genre->id,
                'name' => 'Name Updated',
            ]
        );
    }

    public function testDelete()
    {
        $genre = GenreModel::factory()->create();
        $useCase = new DeleteGenreUseCase($this->repository);

        $response = $this->controller->destroy(useCase: $useCase, id: $genre->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
    }
}
