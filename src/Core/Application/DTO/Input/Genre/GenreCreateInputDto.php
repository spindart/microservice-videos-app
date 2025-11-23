<?php

namespace Core\Application\DTO\Input\Genre;

class GenreCreateInputDto
{
    public function __construct(
        public string $name,
        public array $categoriesId = [],
        public bool $isActive = true,
    ) {}
}
