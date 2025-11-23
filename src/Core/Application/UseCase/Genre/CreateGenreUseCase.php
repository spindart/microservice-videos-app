<?php

namespace Core\Application\UseCase\Genre;

use Core\Application\DTO\Input\Genre\GenreCreateInputDto;
use Core\Application\DTO\Output\Genre\GenreCreateOutputDto;
use Core\Application\Interfaces\DBTransactionInterface;
use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;

class CreateGenreUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected DBTransactionInterface $transaction
    ) {}

    public function execute(GenreCreateInputDto $input): GenreCreateOutputDto
    {

        try {

            $genre = new Genre(
                name: $input->name,
                isActive: $input->isActive,
                categoriesId: $input->categoriesId,
            );
            $this->validateCategories($input->categoriesId);

            $newGenre = $this->repository->insert($genre);
            $this->transaction->commit();

            return new GenreCreateOutputDto(
                id: $newGenre->id(),
                name: $newGenre->name,
                created_at: $newGenre->createdAt(),
                is_active: $newGenre->isActive,
            );
        } catch (\Throwable $th) {
            $this->transaction->rollback();
            throw $th;
        }
    }

    public function validateCategories(array $categoriesId): void
    {
        $existingCategories = $this->categoryRepository->getIdsList($categoriesId);
        $diff = array_diff($categoriesId, $existingCategories);
        if (count($diff)) {
            throw new EntityNotFoundException(
                sprintf('Some categories could not be found: %s', implode(', ', $diff))
            );
        }
    }
}
