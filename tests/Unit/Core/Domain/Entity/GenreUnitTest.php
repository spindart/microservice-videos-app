<?php

namespace Tests\Unit\Core\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityValidationException;
use DateTime;

class GenreUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testAttributes()
    {

        $uuid = Uuid::uuid4()->toString();
        $genre = new Genre(
            id: $uuid,
            name: 'Genre Name',
            isActive: true,
            createdAt: '2025-01-01 10:00:00'
        );

        $this->assertNotEmpty($genre->id());
        $this->assertEquals($uuid, (string)$genre->id());
        $this->assertEquals('Genre Name', $genre->name);
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

        $genre->update('New Name');
        $this->assertEquals('New Name', $genre->name);
    }

        public function testExceptionName()
    {
        try {
            new Genre(
                name: 'Te',
                isActive: true
            );
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Name must be at least 3 characters');
        }
    }


}
