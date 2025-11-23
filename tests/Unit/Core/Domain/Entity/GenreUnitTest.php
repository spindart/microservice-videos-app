<?php

namespace Tests\Unit\Core\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityValidationException;
use DateTime;
use Illuminate\Support\Facades\Date;

class GenreUnitTest extends TestCase
{

    public function testAttributes()
    {

        $uuid = Uuid::uuid4()->toString();
        $date = date('Y-m-d H:i:s');
        $genre = new Genre(
            id: $uuid,
            name: 'Genre Name',
            isActive: true,
            createdAt: new DateTime(date('Y-m-d H:i:s'))
        );

        $this->assertNotEmpty($genre->id());
        $this->assertEquals($uuid, (string)$genre->id());
        $this->assertEquals('Genre Name', $genre->name);
        $this->assertEquals($date, $genre->createdAt());
    }

    public function testAttributesCreate()
    {

        $genre = new Genre(
            name: 'Genre Name',
        );
        $this->assertNotEmpty($genre->id());
        $this->assertEquals('Genre Name', $genre->name);
        $this->assertNotEmpty($genre->createdAt());
        $this->assertTrue($genre->isActive);
    }

    public function testActivated()
    {
        $genre = new Genre(
            name: 'Test Genre',
            isActive: false
        );
        $this->assertFalse($genre->isActive);
        $genre->activate();
        $this->assertTrue($genre->isActive);
    }

    public function testDeactivated()
    {
        $genre = new Genre(name: 'Test Genre',  isActive: true);
        $this->assertTrue($genre->isActive);
        $genre->deactivate();
        $this->assertFalse($genre->isActive);
    }

    public function testUpdate()
    {
        $genre = new Genre(name: 'Old Name');
        $this->assertEquals('Old Name', $genre->name);

        $genre->update(name: 'New Name');
        $this->assertEquals('New Name', $genre->name);
    }

    public function testExceptionName()
    {
        $this->expectException(EntityValidationException::class);

        new Genre(
            name: 'Te',
        );
    }

    public function testExceptionUpdateName()
    {
        $genre = new Genre(name: 'Valid Name');

        $this->expectException(EntityValidationException::class);
        $genre->update(name: 'No');
    }


    public function testAddCategoryToGenre()
    {
        $genre = new Genre(name: 'Genre with Category');
        $categoryId = Uuid::uuid4()->toString();
        $categoryId2 = Uuid::uuid4()->toString();
        $this->assertIsArray($genre->categoriesId);
        $this->assertCount(0, $genre->categoriesId);

        $genre->addCategory(categoryId: $categoryId);
        $genre->addCategory(categoryId: $categoryId2);
        
        $this->assertContains($categoryId, $genre->categoriesId);
        $this->assertCount(2, $genre->categoriesId);
    }

    public function testRemoveCategoryFromGenre()
    {
        $categoryId = Uuid::uuid4()->toString();
        $categoryId2 = Uuid::uuid4()->toString();
        $genre = new Genre(name: 'Genre with Category', categoriesId: [
            $categoryId,
            $categoryId2
        ]);
        $this->assertCount(2, $genre->categoriesId);

        $genre->removeCategory(categoryId: $categoryId);

        $this->assertNotContains($categoryId, $genre->categoriesId);
        $this->assertCount(1, $genre->categoriesId);
        $this->assertContains($categoryId2, $genre->categoriesId);
    }
}
