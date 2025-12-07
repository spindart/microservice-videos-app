<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\CastMember;

interface CastMemberRepositoryInterface
{
    public function insert(CastMember $castMember): CastMember;
    public function findById(string $id): CastMember;
    public function getIdsList(array $ids = []): array;
    public function findAll(string $filter = '', string $order = 'DESC'): array;
    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $perPage = 10): PaginationInterface;
    public function update(CastMember $castMember): CastMember;
    public function delete(string $id): bool;
    public function toCastMember(object $data): CastMember;
}
