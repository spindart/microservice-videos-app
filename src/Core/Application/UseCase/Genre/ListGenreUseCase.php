<?php

namespace Core\Application\UseCase\Genre;

use Core\Application\DTO\Input\Genre\ListGenreInputDto;
use Core\Application\DTO\Output\Genre\ListGenreOutputDto;
use Core\Domain\Repository\GenreRepositoryInterface;

class ListGenreUseCase
{
    public function __construct(private GenreRepositoryInterface $repository) {}

    public function execute(ListGenreInputDto $input): ListGenreOutputDto
    {
        $category = $this->repository->findById($input->id);
        return new ListGenreOutputDto(
            id: $category->id(),
            name: $category->name,
            created_at: $category->createdAt(),
            is_active: $category->isActive,
        );
    }
}
