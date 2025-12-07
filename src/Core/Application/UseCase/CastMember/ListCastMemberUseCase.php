<?php

namespace Core\Application\UseCase\CastMember;

use Core\Application\DTO\Input\CastMember\ListCastMemberInputDto;
use Core\Application\DTO\Output\CastMember\ListCastMemberOutputDto;
use Core\Domain\Repository\CastMemberRepositoryInterface;

class ListCastMemberUseCase
{

    public function  __construct(private CastMemberRepositoryInterface $repository) {}

    public function execute(ListCastMemberInputDto $input): ListCastMemberOutputDto
    {
        $castMember = $this->repository->findById($input->id);

        return new ListCastMemberOutputDto(
            id: $castMember->id(),
            name: $castMember->name,
            type: $castMember->type(),
            is_active: $castMember->isActive,
            created_at: $castMember->createdAt()
        );
    }
}
