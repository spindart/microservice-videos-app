<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Category;
use Core\Domain\Exception\EntityValidationException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new Category(
            name: 'Test Category',
            description: 'Test Description',
            isActive: true
        );
        $this->assertNotEmpty($category->createdAt());
        $this->assertNotEmpty($category->id());
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('Test Description', $category->description);
        $this->assertTrue($category->isActive);
    }

    public function testActivated()
    {
        $category = new Category(
            name: 'Test Category',
            description: 'Test Description',
            isActive: false
        );
        $this->assertFalse($category->isActive);
        $category->activate();
        $this->assertTrue($category->isActive);
    }
    public function testDeactivated()
    {
        $category = new Category(name: 'Test Category', description: 'Test Description', isActive: true);
        $this->assertTrue($category->isActive);
        $category->deactivate();
        $this->assertFalse($category->isActive);
    }

    public function testUpdate()
    {

        $uuid = (string) Uuid::uuid4()->toString();
        $category = new Category(
            id: $uuid,
            name: 'Test Category',
            description: 'Test Description',
            isActive: true,
            createdAt: '2025-01-01 00:00:00'
        );

        $category->update(
            name: 'Test Category Updated',
            description: 'Test Description Updated'
        );
        $this->assertEquals($uuid, $category->id());
        $this->assertEquals('Test Category Updated', $category->name);
        $this->assertEquals('Test Description Updated', $category->description);
        $this->assertEquals('2025-01-01 00:00:00', $category->createdAt());
    }

    public function testExceptionName()
    {
        try {
            new Category(
                name: 'Te',
                description: 'Test Description',
                isActive: true
            );
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Name must be at least 3 characters');
        }
    }

    public function testExceptionDescription()
    {
        try {
            new Category(
                name: 'Test Category',
                description: random_bytes(999999),
                isActive: true
            );
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Description must be less than 255 characters');
        }
    }
}
