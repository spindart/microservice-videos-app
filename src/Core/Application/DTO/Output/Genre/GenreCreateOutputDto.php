<?php

namespace Core\Application\DTO\Output\Genre;

class GenreCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $created_at,
        public bool $is_active = true,
    ) {}
}
