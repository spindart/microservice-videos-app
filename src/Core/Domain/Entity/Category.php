<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Exception\EntityValidationException;
use Core\Domain\Entity\Traits\MagicMethodsTrait;
use Core\Domain\Entity\Validation\DomainValidation;

class Category
{
    use MagicMethodsTrait;
    public function __construct(
        protected string $id = '',
        protected string $name,
        protected string $description,
        protected bool $isActive = true
    ) {
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

    public function validate()
    {
        DomainValidation::strMaxLength($this->name, 255, 'Name must be less than 255 characters');
        DomainValidation::strMinLength($this->name, 3, 'Name must be at least 3 characters');
        DomainValidation::strMaxLength($this->description, 255, 'Description must be less than 255 characters');
        DomainValidation::strCanBeNullAndMaxLength($this->description, 255, 'Description must be at least 3 characters');
    }
}
