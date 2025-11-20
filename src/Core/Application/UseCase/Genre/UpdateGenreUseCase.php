<?php

namespace Core\Application\UseCase\Genre;

use Core\Application\DTO\Input\Genre\GenreUpdateInputDto;
use Core\Application\DTO\Output\Genre\GenreUpdateOutputDto;
use Core\Domain\Repository\GenreRepositoryInterface;

class UpdateGenreUseCase
{
    public function __construct(private GenreRepositoryInterface $repository) {}
    public function execute(GenreUpdateInputDto $input): GenreUpdateOutputDto
    {
        $genre = $this->repository->findById($input->id);
        
        $genre->update(
            name: $input->name,
        );
        
        if ($input->isActive !== null) {
            $input->isActive ? $genre->activate() : $genre->deactivate();
        }
        
        $genreUpdated = $this->repository->update($genre);
        return new GenreUpdateOutputDto(
            id: $genreUpdated->id(),
            name: $genreUpdated->name,
            created_at: $genreUpdated->createdAt(),
            is_active: $genreUpdated->isActive,
        );
    }
}
