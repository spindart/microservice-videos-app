<?php

namespace Core\Application\Interfaces;

interface DBTransactionInterface
{
    public function commit(): void;
    public function rollBack(): void;
}
