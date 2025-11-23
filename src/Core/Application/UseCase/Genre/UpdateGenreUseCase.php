<?php

namespace Core\Application\UseCase\Genre;

use Core\Application\DTO\Input\Genre\GenreUpdateInputDto;
use Core\Application\DTO\Output\Genre\GenreUpdateOutputDto;
use Core\Application\Interfaces\DBTransactionInterface;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;

class UpdateGenreUseCase
{
    public function __construct(
        private GenreRepositoryInterface $repository,
        private CategoryRepositoryInterface $categoryRepository,
        private DBTransactionInterface $transaction
    ) {}

    public function execute(GenreUpdateInputDto $input): GenreUpdateOutputDto
    {
        $genre = $this->repository->findById($input->id);
        $genre->update(
            name: $input->name,
        );

        foreach ($input->categoriesId as $categoryId) {
            if (!in_array($categoryId, $genre->categoriesId)) {
                $genre->addCategory($categoryId);
            }
        }

        try {

            $this->validateCategories($input->categoriesId);

            $newGenre = $this->repository->update($genre);
            $this->transaction->commit();

            return new GenreUpdateOutputDto(
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
