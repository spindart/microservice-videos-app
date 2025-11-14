<?php

namespace Core\Application\UseCase\Category;

use Core\Application\DTO\Input\Category\DeleteCategoryInputDto;
use Core\Application\DTO\Output\Category\DeleteCategoryOutputDto;
use Core\Domain\Repository\CategoryRepositoryInterface;

class DeleteCategoryUseCase
{
    public function __construct(private CategoryRepositoryInterface $repository) {}
    public function execute(DeleteCategoryInputDto $input): DeleteCategoryOutputDto
    {
        $success = $this->repository->delete($input->id);
        return new DeleteCategoryOutputDto(success: $success);
    }
}
