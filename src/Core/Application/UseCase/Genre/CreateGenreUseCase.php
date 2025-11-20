<?php

namespace Core\Application\UseCase\Genre;

use Core\Application\DTO\Input\Genre\GenreCreateInputDto;
use Core\Application\DTO\Output\Genre\GenreCreateOutputDto;
use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;

class CreateGenreUseCase
{
    public function __construct(
        private GenreRepositoryInterface $repository
    ) {}

    public function execute(GenreCreateInputDto $input): GenreCreateOutputDto
    {
        $genre = new Genre(
            name: $input->name,
            isActive: $input->isActive,
        );

        $newGenre = $this->repository->insert($genre);

        return new GenreCreateOutputDto(
            id: $newGenre->id(),
            name: $newGenre->name,
            created_at: $newGenre->createdAt(),
            is_active: $newGenre->isActive,
        );
    }
}
