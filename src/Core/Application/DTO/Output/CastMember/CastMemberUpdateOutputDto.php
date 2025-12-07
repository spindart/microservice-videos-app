<?php

namespace Core\Application\DTO\Output\CastMember;

use Core\Domain\Enum\CastMemberType;

class CastMemberUpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public CastMemberType $type,
        public string $created_at,
        public bool $is_active = true,
    ) {}
}
