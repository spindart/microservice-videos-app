<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Requests\CastMember\{
    StoreCastMemberRequest, UpdateCastMemberRequest};
use App\Http\Controllers\Api\CastMemberController;
use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\Application\UseCase\CastMember\CreateCastMemberUseCase;
use Core\Application\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\Application\UseCase\CastMember\ListCastMembersUseCase;
use Core\Application\UseCase\CastMember\ListCastMemberUseCase;
use Core\Application\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\Domain\Enum\CastMemberType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CastMemberControllerTest extends TestCase
{
    protected $repository;
    protected $controller;

    protected function setUp(): void
    {
        $this->repository = new CastMemberRepositoryEloquent(
            new CastMemberModel()
        );

        $this->controller = new CastMemberController();
        parent::setUp();
    }

    public function testIndex()
    {
        $useCase = new ListCastMembersUseCase($this->repository);

        $response = $this->controller->index(new Request(), $useCase);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $useCase = new CreateCastMemberUseCase($this->repository);

        $request = new StoreCastMemberRequest();
        $request->headers->set('content-type', 'application/json');

        $request->setJson(new ParameterBag([
            'name' => 'Test',
            'type' => CastMemberType::ACTOR
        ]));


        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    public function testShow()
    {
        $castMember = CastMemberModel::factory()->create();
        $useCase = new ListCastMemberUseCase($this->repository);
        $response = $this->controller->show(
            useCase: $useCase,
            id: $castMember->id,
        );
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate()
    {
        $castMember = CastMemberModel::factory()->create();
        $useCase = new UpdateCastMemberUseCase($this->repository);

        $request = new UpdateCastMemberRequest();
        $request->headers->set('content-type', 'application/json');

        $request->setJson(new ParameterBag([
            'name' => 'Name Updated',
            'type' => CastMemberType::ACTOR
        ]));

        $response = $this->controller->update(request: $request, useCase: $useCase, id: $castMember->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas(
            'cast_members',
            [
                'id' => $castMember->id,
                'name' => 'Name Updated',
                'type' => CastMemberType::ACTOR
            ]
        );
    }

    public function testDelete()
    {
        $castMember = CastMemberModel::factory()->create();
        $useCase = new DeleteCastMemberUseCase($this->repository);

        $response = $this->controller->destroy(useCase: $useCase, id: $castMember->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
    }
}
