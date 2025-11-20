<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use Core\Application\DTO\Input\Genre\ListGenresInputDto;
use Core\Application\UseCase\Genre\ListGenresUseCase;
use Illuminate\Http\Request;

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
}
