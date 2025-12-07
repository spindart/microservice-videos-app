<?php

namespace Core\Application\UseCase\CastMember;

use cORE\Application\DTO\Input\CastMember\ListCastMembersInputDto;
use Core\Application\DTO\Output\CastMember\ListCastMembersOutputDto;
use Core\Domain\Repository\CastMemberRepositoryInterface;

class ListCastMembersUseCase
{

    public function __construct(private CastMemberRepositoryInterface $repository) {}

    public function execute(ListCastMembersInputDto $input): ListCastMembersOutputDto
    {

        $castMembers = $this->repository->paginate(
            $input->filter,
            $input->order,
            $input->page,
            $input->perPage
        );

        return new ListCastMembersOutputDto(
            items: $castMembers->items(),
            total: $castMembers->total(),
            current_page: $castMembers->currentPage(),
            per_page: $castMembers->perPage(),
            last_page: $castMembers->lastPage(),
            from: $castMembers->from(),
            to: $castMembers->to(),
            first_page: $castMembers->firstPage(),
            next_page: $castMembers->nextPage(),
            previous_page: $castMembers->previousPage(),
        );
    }
}
