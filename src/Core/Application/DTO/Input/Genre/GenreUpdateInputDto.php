<?php

namespace Core\Application\DTO\Input\Genre;

class GenreUpdateInputDto
{
    public function __construct(
        public string $id,
        public string|null $name = null,
        public bool|null $isActive = null,
    ) {}
}
