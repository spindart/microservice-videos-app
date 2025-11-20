<?php

namespace Core\Application\DTO\Output\Genre;

class DeleteGenreOutputDto
{
    public function __construct(
        public bool $success,
    ) {}
}
