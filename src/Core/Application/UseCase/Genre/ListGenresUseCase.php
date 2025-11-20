<?php

namespace Core\Application\UseCase\Genre;

use Core\Application\DTO\Input\Genre\ListGenresInputDto;
use Core\Application\DTO\Output\Genre\ListGenresOutputDto;
use Core\Domain\Repository\GenreRepositoryInterface;

class ListGenresUseCase
{
    public function __construct(private GenreRepositoryInterface $repository) {}

    public function execute(ListGenresInputDto $input): ListGenresOutputDto
    {
        $categories = $this->repository->paginate(
            $input->filter,
            $input->order,
            $input->page,
            $input->perPage
        );

        return new ListGenresOutputDto(
            items: $categories->items(),
            total: $categories->total(),
            current_page: $categories->currentPage(),
            per_page: $categories->perPage(),
            last_page: $categories->lastPage(),
            from: $categories->from(),
            to: $categories->to(),
            first_page: $categories->firstPage(),
            next_page: $categories->nextPage(),
            previous_page: $categories->previousPage(),
        );

    }
}
