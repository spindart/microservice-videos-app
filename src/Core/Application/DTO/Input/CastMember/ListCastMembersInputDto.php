<?php

namespace cORE\Application\DTO\Input\CastMember;

class ListCastMembersInputDto
{

    public function __construct(public string $filter = '', public string $order = 'DESC', public int $page = 1, public int $perPage = 10) {}
}
