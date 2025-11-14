<?php

namespace Core\Application\DTO\Output\Category;

class DeleteCategoryOutputDto
{
    public function __construct(
        public bool $success,
    ) {}
}
