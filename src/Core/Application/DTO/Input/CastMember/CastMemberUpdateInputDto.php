<?php

namespace Core\Application\DTO\Input\CastMember;

use Core\Domain\Enum\CastMemberType;

class CastMemberUpdateInputDto
{

    public function __construct(
        public string $id,
        public string|null $name = null,
        public CastMemberType|null $type = null,
        public bool|null $isActive = null
    ) {}
}
