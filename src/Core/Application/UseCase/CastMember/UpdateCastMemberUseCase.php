<?php

namespace Core\Application\UseCase\CastMember;

use Core\Application\DTO\Input\CastMember\CastMemberUpdateInputDto;
use Core\Application\DTO\Output\CastMember\CastMemberUpdateOutputDto;
use Core\Domain\Repository\CastMemberRepositoryInterface;

class UpdateCastMemberUseCase
{

    public function __construct(private CastMemberRepositoryInterface $repository) {}

    public function execute(CastMemberUpdateInputDto $input): CastMemberUpdateOutputDto
    {
        $castMember = $this->repository->findById($input->id);

        $castMember->update(
            name: $input->name ?? $castMember->name,
            type: $input->type ?? $castMember->type(),
        );

        $castMemberUpdated = $this->repository->update($castMember);

        return new CastMemberUpdateOutputDto(
            id: $castMemberUpdated->id,
            name: $castMemberUpdated->name,
            type: $castMemberUpdated->type(),
            created_at: $castMemberUpdated->createdAt(),
            is_active: $castMemberUpdated->isActive
        );
    }
}
