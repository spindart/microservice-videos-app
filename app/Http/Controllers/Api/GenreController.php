<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Genre\{StoreGenreRequest, UpdateGenreRequest};
use App\Http\Resources\GenreResource;
use Core\Application\DTO\Input\Genre\DeleteGenreInputDto;
use Core\Application\DTO\Input\Genre\GenreCreateInputDto;
use Core\Application\DTO\Input\Genre\GenreUpdateInputDto;
use Core\Application\DTO\Input\Genre\ListGenreInputDto;
use Core\Application\DTO\Input\Genre\ListGenresInputDto;
use Core\Application\UseCase\Genre\CreateGenreUseCase;
use Core\Application\UseCase\Genre\DeleteGenreUseCase;
use Core\Application\UseCase\Genre\ListGenresUseCase;
use Core\Application\UseCase\Genre\ListGenreUseCase;
use Core\Application\UseCase\Genre\UpdateGenreUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    public function index(Request $request, ListGenresUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new ListGenresInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', ''),
                page: (int) $request->get('page', 1),
                perPage: (int) $request->get('perPage', 10)
            )
        );

        return GenreResource::collection(collect($response->items))->additional([
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

    public function store(StoreGenreRequest $request, CreateGenreUseCase $useCase)
    {
        $response = $useCase->execute(input: new GenreCreateInputDto(
            name: $request->name,
            categoriesId: $request->categories_id ?? [],
            isActive: $request->is_active ?? true
        ));

        return (new GenreResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ListGenreUseCase $useCase, string $id)
    {
        $genre = $useCase->execute(new ListGenreInputDto($id));
        return (new GenreResource($genre))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateGenreRequest $request, UpdateGenreUseCase $useCase, string $id)
    {
        $genre = $useCase->execute(new GenreUpdateInputDto(
            id: $id,
            name: $request->name,
            categoriesId: $request->categories_id ?? [],
            isActive: $request->is_active ?? true

        ));

        return (new GenreResource($genre))
            ->response();
    }

    public function destroy(DeleteGenreUseCase $useCase, string $id)
    {
        $useCase->execute(new DeleteGenreInputDto(id: $id));

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
