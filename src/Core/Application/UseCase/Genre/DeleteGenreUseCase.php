<?php

namespace Core\Application\UseCase\Genre;

use Core\Application\DTO\Input\Genre\DeleteGenreInputDto;
use Core\Application\DTO\Output\Genre\DeleteGenreOutputDto;
use Core\Domain\Repository\GenreRepositoryInterface;

class DeleteGenreUseCase
{
    public function __construct(private GenreRepositoryInterface $repository) {}
    public function execute(DeleteGenreInputDto $input): DeleteGenreOutputDto
    {
        $success = $this->repository->delete($input->id);
        return new DeleteGenreOutputDto(success: $success);
    }
}
