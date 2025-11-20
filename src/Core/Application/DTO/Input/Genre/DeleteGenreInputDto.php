<?php

namespace Core\Application\DTO\Input\Genre;

class DeleteGenreInputDto
{
    public function __construct(
        public string $id,
    ) {}
}
