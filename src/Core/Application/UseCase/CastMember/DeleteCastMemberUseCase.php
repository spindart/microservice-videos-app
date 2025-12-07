<?php

namespace Core\Application\UseCase\CastMember;

use Core\Application\DTO\Input\CastMember\DeleteCastMemberInputDto;
use Core\Application\DTO\Output\CastMember\DeleteCastMemberOutputDto;
use Core\Domain\Repository\CastMemberRepositoryInterface;

class DeleteCastMemberUseCase
{

    public function __construct(private CastMemberRepositoryInterface $repository) {}

    public function execute(DeleteCastMemberInputDto $input): DeleteCastMemberOutputDto
    {

        $response = $this->repository->delete($input->id);

        return new DeleteCastMemberOutputDto(success: $response);
    }
}
