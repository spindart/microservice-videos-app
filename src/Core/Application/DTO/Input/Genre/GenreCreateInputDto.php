<?php

namespace Core\Application\DTO\Input\Genre;

class GenreCreateInputDto
{
    public function __construct(
        public string $name,
        public bool $isActive = true,
    ) {}
}
