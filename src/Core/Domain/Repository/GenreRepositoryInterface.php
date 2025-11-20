<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Genre;

interface GenreRepositoryInterface
{
    public function insert(Genre $category): Genre;
    public function findById(string $id): Genre;
    public function findAll(string $filter = '', string $order = 'DESC'): array;
    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $perPage = 10): PaginationInterface;
    public function update(Genre $category): Genre;
    public function delete(string $id): bool;
    public function toCategory(object $data): Genre;
}
