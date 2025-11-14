<?php

namespace Core\Application\DTO\Input\Category;

class DeleteCategoryInputDto
{
    public function __construct(
        public string $id,
    ) {}
}
