<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Category;
use Core\Domain\Entity\Exception\EntityValidationException;
use PHPUnit\Framework\TestCase;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new Category(
            id: '1',
            name: 'Test Category',
            description: 'Test Description',
            isActive: true
        );
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('Test Description', $category->description);
        $this->assertTrue($category->isActive);
    }

    public function testActivated()
    {
        $category = new Category(
            id: '1',
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
        $category = new Category(id: '1', name: 'Test Category', description: 'Test Description', isActive: true);
        $this->assertTrue($category->isActive);
        $category->deactivate();
        $this->assertFalse($category->isActive);
    }

    public function testUpdate()
    {

        $uuid = 'uuid.value';
        $category = new Category(
            id: $uuid,
            name: 'Test Category',
            description: 'Test Description',
            isActive: true
        );

        $category->update(
            name: 'Test Category Updated',
            description: 'Test Description Updated'
        );

        $this->assertEquals('Test Category Updated', $category->name);
        $this->assertEquals('Test Description Updated', $category->description);
    }

    public function testExceptionName()
    {
        try {
            new Category(
                id: '1',
                name: 'Te',
                description: 'Test Description',
                isActive: true
            );
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Name must be at least 3 characters');
        }
    }
}
