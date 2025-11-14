<?php

namespace Core\Application\DTO\Input\Category;

class CategoryUpdateInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string|null $description,
        public bool|null $isActive = true,
    ) {}
}
