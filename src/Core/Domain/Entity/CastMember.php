<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MagicMethodsTrait;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Core\Domain\Enum\CastMemberType;

class CastMember
{
    use MagicMethodsTrait;

    public function __construct(
        protected Uuid|string $id = '',
        protected string $name = '',
        protected CastMemberType $type = CastMemberType::ACTOR,
        protected DateTime|null $createdAt = null,
        protected bool $isActive = true,
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

    public function update(string $name, CastMemberType $type)
    {
        $this->name = $name;
        $this->type = $type;

        $this->validate();
    }

    public function type(): CastMemberType
    {
        return $this->type;
    }

    private function validate()
    {
        DomainValidation::strMaxLength($this->name, 255, 'Name must be less than 255 characters');
        DomainValidation::strMinLength($this->name, 3, 'Name must be at least 3 characters');
    }
}
