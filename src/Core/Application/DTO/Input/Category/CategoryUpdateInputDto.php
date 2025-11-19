<?php

namespace Core\Application\DTO\Input\Category;

class CategoryUpdateInputDto
{
    public function __construct(
        public string $id,
        public string|null $name = null,
        public string|null $description = null,
        public bool|null $isActive = null,
    ) {}
}
