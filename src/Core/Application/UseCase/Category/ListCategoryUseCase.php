<?php

namespace Core\Application\UseCase\Category;

use Core\Application\DTO\Input\Category\ListCategoryInputDto;
use Core\Application\DTO\Output\Category\ListCategoryOutputDto;
use Core\Domain\Repository\CategoryRepositoryInterface;

class ListCategoryUseCase
{
    public function __construct(private CategoryRepositoryInterface $repository) {}

    public function execute(ListCategoryInputDto $input): ListCategoryOutputDto
    {
        $categories = $this->repository->findById($input->id);
        return new ListCategoryOutputDto(
            id: $categories->id(),
            name: $categories->name,
            description: $categories->description,
            created_at: $categories->createdAt(),
            is_active: $categories->isActive,
        );
    }
}
