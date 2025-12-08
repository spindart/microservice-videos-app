<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CastMember\{
    StoreCastMemberRequest,
    UpdateCastMemberRequest
};
use App\Http\Resources\CastMemberResource;
use Core\Application\DTO\Input\CastMember\{
    CastMemberCreateInputDto,
    CastMemberUpdateInputDto,
    DeleteCastMemberInputDto,
    ListCastMembersInputDto,
    ListCastMemberInputDto
};
use Core\Application\UseCase\CastMember\{
    CreateCastMemberUseCase,
    DeleteCastMemberUseCase,
    ListCastMembersUseCase,
    ListCastMemberUseCase,
    UpdateCastMemberUseCase
};
use Core\Domain\Enum\CastMemberType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CastMemberController extends Controller
{
    public function index(Request $request, ListCastMembersUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new ListCastMembersInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', ''),
                page: (int) $request->get('page', 1),
                perPage: (int) $request->get('perPage', 10)
            )
        );

        return CastMemberResource::collection(collect($response->items))->additional([
            'meta' => [
                'total' => $response->total,
                'current_page' => $response->current_page,
                'per_page' => $response->per_page,
                'last_page' => $response->last_page,
                'from' => $response->from,
                'to' => $response->to,
                'first_page' => $response->first_page,
                'next_page' => $response->next_page,
                'previous_page' => $response->previous_page
            ]
        ]);
    }

    public function store(StoreCastMemberRequest $request, CreateCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(input: new CastMemberCreateInputDto(
            name: $request->name,
            type: CastMemberType::tryFrom($request->type),
            isActive: $request->is_active ?? true
        ));

        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ListCastMemberUseCase $useCase, string $id)
    {
        $category = $useCase->execute(new ListCastMemberInputDto($id));
        return (new CastMemberResource($category))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateCastMemberRequest $request, UpdateCastMemberUseCase $useCase, string $id)
    {
        $category = $useCase->execute(new CastMemberUpdateInputDto(
            id: $id,
            name: $request->name,
            type: CastMemberType::tryFrom($request->type),
            isActive: $request->is_active ?? true

        ));

        return (new CastMemberResource($category))
            ->response();
    }

    public function destroy(DeleteCastMemberUseCase $useCase, string $id)
    {
        $useCase->execute(new DeleteCastMemberInputDto(id: $id));

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}