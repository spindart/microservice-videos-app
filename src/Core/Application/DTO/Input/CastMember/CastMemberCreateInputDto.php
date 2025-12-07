<?php

namespace Core\Application\DTO\Input\CastMember;

use Core\Domain\Enum\CastMemberType;

class CastMemberCreateInputDto
{
    public function __construct(
        public string $name,
        public CastMemberType $type,
        public bool $isActive = true,
    ) {}
}