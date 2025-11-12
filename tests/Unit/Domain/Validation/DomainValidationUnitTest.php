<?php

namespace Tests\Unit\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use PHPUnit\Framework\TestCase;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNull()
    {
        try {
            $value = '';
            DomainValidation::notNull($value);
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }
    public function testNotNullWithMessage()
    {
        try {
            $value = '';
            DomainValidation::notNull($value, 'Value must be not empty or null');
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Value must be not empty or null');
        }
    }

    public function testStrMaxLength()
    {
        try {
            $value = '12345678910';
            DomainValidation::strMaxLength($value, 10, 'Value must be less than 10 characters');
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Value must be less than 10 characters');
        }
    }

    public function testStrMinLength()
    {
        try {
            $value = '12';
            DomainValidation::strMinLength($value, 3, 'Value must be at least 3 characters');
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Value must be at least 3 characters');
        }
    }
    public function testStrCanBeNullAndMaxLength()
    {
        try {
            $value = '12345678910';
            DomainValidation::strCanBeNullAndMaxLength($value, 10, 'Value must be less than 10 characters');
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Value must be less than 10 characters');
        }
    }
}
