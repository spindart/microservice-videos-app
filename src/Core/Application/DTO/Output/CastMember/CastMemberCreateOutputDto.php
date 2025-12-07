<?php

namespace Core\Application\DTO\Output\CastMember;

class CastMemberCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $type,
        public bool $is_active,
        public string $created_at,
    ) {}
}