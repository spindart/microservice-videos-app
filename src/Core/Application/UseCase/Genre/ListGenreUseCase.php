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
        $categories = $this->repository->findById($input->id);
        return new ListGenreOutputDto(
            id: $categories->id(),
            name: $categories->name,
            created_at: $categories->createdAt(),
            is_active: $categories->isActive,
        );
    }
}
