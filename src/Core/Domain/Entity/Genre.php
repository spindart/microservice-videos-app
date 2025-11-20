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
    
        public function update(?string $name = null)
    {
        $this->name = $name ?? $this->name;

        $this->validate();
    }

    private function validate()
    {
        DomainValidation::strMaxLength($this->name, 255, 'Name must be less than 255 characters');
        DomainValidation::strMinLength($this->name, 3, 'Name must be at least 3 characters');
    }
}
