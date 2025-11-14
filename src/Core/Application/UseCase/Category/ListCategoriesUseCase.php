<?php

namespace Core\Application\UseCase\Category;

use Core\Application\DTO\Input\Category\ListCategoriesInputDto;
use Core\Application\DTO\Output\Category\ListCategoriesOutputDto;
use Core\Domain\Repository\CategoryRepositoryInterface;

class ListCategoriesUseCase
{
    public function __construct(private CategoryRepositoryInterface $repository) {}

    public function execute(ListCategoriesInputDto $input): ListCategoriesOutputDto
    {
        $categories = $this->repository->paginate(
            $input->filter,
            $input->order,
            $input->page,
            $input->perPage
        );

        return new ListCategoriesOutputDto(
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

        // return new ListCategoriesOutputDto(
        //     items: array_map(function ($data) {
        //         return [
        //             'id' => $data->id(),
        //             'name' => $data->name,
        //             'description' => $data->description,
        //             'is_active' => (bool) $data->isActive,
        //             'created_at' => (string) $data->createdAt(),
        //         ];
        //     }, $categories->items()),
        //     total: $categories->total(),
        //     current_page: $categories->currentPage(),
        //     per_page: $categories->perPage(),
        //     last_page: $categories->lastPage(),
        //     from: $categories->from(),
        //     to: $categories->to(),
        //     first_page: $categories->firstPage(),
        //     next_page: $categories->nextPage(),
        //     previous_page: $categories->previousPage(),
        // );
    }
}
