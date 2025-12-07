<?php

namespace Core\Application\UseCase\CastMember;

use Core\Application\DTO\Input\CastMember\CastMemberCreateInputDto;
use Core\Application\DTO\Output\CastMember\CastMemberCreateOutputDto;
use Core\Domain\Entity\CastMember;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;

class CreateCastMemberUseCase
{
    public function __construct(
        protected CastMemberRepositoryInterface $repository,
    ) {}

    public function execute(CastMemberCreateInputDto $input): CastMemberCreateOutputDto
    {
        $castMember = new CastMember(
            name: $input->name,
            type: $input->type,
            isActive: $input->isActive,
        );

        $newCastMember = $this->repository->insert($castMember);

        return new CastMemberCreateOutputDto(
            id: $newCastMember->id(),
            name: $newCastMember->name,
            type: $newCastMember->type(),
            is_active: $newCastMember->isActive,
            created_at: $newCastMember->createdAt(),
        );
    }
}
