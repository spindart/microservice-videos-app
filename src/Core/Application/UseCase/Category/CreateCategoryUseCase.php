<?php

namespace Core\Application\UseCase\Category;

use Core\Application\DTO\Input\Category\CategoryCreateInputDto;
use Core\Application\DTO\Output\Category\CategoryCreateOutputDto;
use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;

class CreateCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $repository
    ) {}

    public function execute(CategoryCreateInputDto $input): CategoryCreateOutputDto
    {
        $category = new Category(
            name: $input->name,
            description: $input->description,
            isActive: $input->isActive,
        );

        $newCategory = $this->repository->insert(category: $category);

        return new CategoryCreateOutputDto(
            id: $newCategory->id(),
            name: $newCategory->name,
            description: $newCategory->description,
            created_at: $newCategory->createdAt(),
            is_active: $newCategory->isActive,
        );
    }
}
