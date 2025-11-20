<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Core\Application\DTO\Input\Category\{
    CategoryCreateInputDto,
    CategoryUpdateInputDto,
    DeleteCategoryInputDto,
    ListCategoriesInputDto,
    ListCategoryInputDto
};
use Core\Application\UseCase\Category\{
    CreateCategoryUseCase,
    DeleteCategoryUseCase,
    ListCategoriesUseCase,
    ListCategoryUseCase,
    UpdateCategoryUseCase
};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;

class CategoryController extends Controller
{
    public function index(Request $request, ListCategoriesUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new ListCategoriesInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', ''),
                page: (int) $request->get('page', 1),
                perPage: (int) $request->get('perPage', 10)
            )
        );

        return CategoryResource::collection(collect($response->items))->additional([
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

    public function store(StoreCategoryRequest $request, CreateCategoryUseCase $useCase)
    {
        $response = $useCase->execute(input: new CategoryCreateInputDto(
            name: $request->name,
            description: $request->description ?? '',
        ));

        return (new CategoryResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ListCategoryUseCase $useCase, string $id)
    {
        $category = $useCase->execute(new ListCategoryInputDto($id));
        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateCategoryRequest $request, UpdateCategoryUseCase $useCase, string $id)
    {
        $category = $useCase->execute(new CategoryUpdateInputDto(
            id: $id,
            name: $request->name,
            description: $request->description,
            isActive: $request->is_active ?? true

        ));

        return (new CategoryResource($category))
            ->response();
    }

    public function destroy(DeleteCategoryUseCase $useCase, string $id)
    {
        $useCase->execute(new DeleteCategoryInputDto(id: $id));

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
