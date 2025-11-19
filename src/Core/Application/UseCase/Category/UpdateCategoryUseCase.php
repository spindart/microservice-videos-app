<?php

namespace Core\Application\UseCase\Category;

use Core\Application\DTO\Input\Category\CategoryUpdateInputDto;
use Core\Application\DTO\Output\Category\CategoryUpdateOutputDto;
use Core\Domain\Repository\CategoryRepositoryInterface;

class UpdateCategoryUseCase
{
    public function __construct(private CategoryRepositoryInterface $repository) {}
    public function execute(CategoryUpdateInputDto $input): CategoryUpdateOutputDto
    {
        $category = $this->repository->findById($input->id);
        
        $category->update(
            name: $input->name,
            description: $input->description
        );
        
        if ($input->isActive !== null) {
            $input->isActive ? $category->activate() : $category->deactivate();
        }
        
        $categoryUpdated = $this->repository->update($category);
        return new CategoryUpdateOutputDto(
            id: $categoryUpdated->id(),
            name: $categoryUpdated->name,
            description: $categoryUpdated->description,
            created_at: $categoryUpdated->createdAt(),
            is_active: $categoryUpdated->isActive,
        );
    }
}
