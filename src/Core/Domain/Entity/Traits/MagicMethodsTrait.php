<?php

namespace Core\Domain\Entity\Traits;

trait MagicMethodsTrait
{
    public function __get(string $property)
    {
        if (isset($this->{$property}))
            return $this->{$property};
        $className = get_class($this);
        throw new \Exception("Property {$property} not found in class {$className}");
    }

    public function id(): string
    {
        return (string) $this->id;
    }

    public function createdAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->createdAt->format($format);
    }
}
