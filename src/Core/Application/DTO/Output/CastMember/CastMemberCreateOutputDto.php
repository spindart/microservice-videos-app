<?php

namespace Core\Application\DTO\Output\CastMember;

use Core\Domain\Enum\CastMemberType;

class CastMemberCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public CastMemberType $type,
        public bool $is_active,
        public string $created_at,
    ) {}
}