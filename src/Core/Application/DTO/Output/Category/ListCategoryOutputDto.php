<?php

namespace Core\Application\DTO\Output\Category;

class ListCategoryOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $created_at,
        public bool $is_active = true,
    ) {}
}

