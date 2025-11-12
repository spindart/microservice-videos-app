<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MagicMethodsTrait;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Category
{
    use MagicMethodsTrait;
    public function __construct(
        protected string $name,
        protected string $description,
        protected Uuid|string|null $id = null,
        protected bool $isActive = true,
        protected DateTime|string $createdAt = '',
    ) {
        $this->id = $id ? new Uuid($id) : Uuid::random();
        $this->createdAt = $createdAt ? new DateTime($createdAt) : new DateTime();

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
    public function update(string $name, ?string $description = null)
    {
        $this->name = $name;
        $this->description = $description ?? $this->description;

        $this->validate();
    }

    private function validate()
    {
        DomainValidation::strMaxLength($this->name, 255, 'Name must be less than 255 characters');
        DomainValidation::strMinLength($this->name, 3, 'Name must be at least 3 characters');
        DomainValidation::strMaxLength($this->description, 255, 'Description must be less than 255 characters');
        DomainValidation::strCanBeNullAndMaxLength($this->description, 255, 'Description must be at least 3 characters');
    }
}
