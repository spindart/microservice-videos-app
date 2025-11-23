<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MagicMethodsTrait;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Genre
{
    use MagicMethodsTrait;


    public function __construct(
        protected Uuid|string $id = '',
        protected string $name = '',
        protected bool $isActive = true,
        protected array $categoriesId = [],
        protected DateTime|null $createdAt = null,
    ) {
        $this->id = $id ? new Uuid($id) : Uuid::random();
        $this->createdAt = $createdAt ?? new DateTime();

        $this->validate();
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function update(string $name)
    {
        $this->name = $name;

        $this->validate();
    }

    public function addCategory(string $categoryId): void
    {
        array_push($this->categoriesId, $categoryId);
    }
    
    public function removeCategory(string $categoryId): void
    {
        $this->categoriesId = array_filter(
            $this->categoriesId,
            fn($id) => $id !== $categoryId
        );
    }

    private function validate()
    {
        DomainValidation::strMaxLength($this->name, 255, 'Name must be less than 255 characters');
        DomainValidation::strMinLength($this->name, 3, 'Name must be at least 3 characters');
    }
}
