<?php

namespace Tests\Unit\Core\Domain\Entity;

use Core\Domain\Exception\EntityValidationException;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;

class CastMemberUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = Uuid::uuid4()->toString();

        $date = date('Y-m-d H:i:s');
        $castMember = new \Core\Domain\Entity\CastMember(
            id: $uuid,
            name: 'Test Cast Member',
            type: CastMemberType::ACTOR,
            createdAt: new DateTime(date('Y-m-d H:i:s')),
            isActive: true
        );
        $this->assertNotEmpty($castMember->createdAt());
        $this->assertNotEmpty($castMember->id());
        $this->assertEquals('Test Cast Member', $castMember->name);
        $this->assertEquals(CastMemberType::ACTOR, $castMember->type);
        $this->assertEquals($date, $castMember->createdAt());
    }

    public function testExceptionWhenNameIsTooLong()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('Name must be less than 255 characters');

        new CastMember(
            name: str_repeat('a', 256),
            type: CastMemberType::DIRECTOR,
        );
    }

    public function testExceptionWhenNameIsTooShort()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('Name must be at least 3 characters');

        new CastMember(
            name: 'aa',
            type: CastMemberType::DIRECTOR,
        );
    }

    public function testUpdateCastMember()
    {
        $castMember = new CastMember(
            name: 'Initial Name',
            type: CastMemberType::ACTOR,
        );

        $castMember->update('Updated Name', CastMemberType::DIRECTOR);

        $this->assertEquals('Updated Name', $castMember->name);
        $this->assertEquals(CastMemberType::DIRECTOR, $castMember->type);
    }

    public function testTypeMethod()
    {
        $castMember = new CastMember(
            name: 'Sample Name',
            type: CastMemberType::DIRECTOR,
        );

        $this->assertEquals(CastMemberType::DIRECTOR, $castMember->type());
    }

    public function testExceptionWhenUpdatingWithInvalidName()
    {
        $castMember = new CastMember(
            name: 'Valid Name',
            type: CastMemberType::ACTOR,
        );

        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('Name must be at least 3 characters');

        $castMember->update('aa', CastMemberType::DIRECTOR);
    }

    public function testExceptionWhenUpdatingWithTooLongName()
    {
        $castMember = new CastMember(
            name: 'Valid Name',
            type: CastMemberType::ACTOR,
        );

        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('Name must be less than 255 characters');

        $castMember->update(str_repeat('a', 256), CastMemberType::DIRECTOR);
    }

    public function testCreateWithSpecificIdAndCreatedAt()
    {
        $specificId = Uuid::uuid4()->toString();
        $date = date('Y-m-d H:i:s');

        $castMember = new CastMember(
            id: $specificId,
            name: 'Test Name',
            type: CastMemberType::ACTOR,
            createdAt: new DateTime(date('Y-m-d H:i:s'))
        );

        $this->assertEquals($specificId, (string)$castMember->id());
        $this->assertEquals($date, $castMember->createdAt());
    }

    public function testActivateAndDeactivate()
    {
        $castMember = new CastMember(
            name: 'Test Name',
            type: CastMemberType::DIRECTOR,
            isActive: false
        );

        $this->assertFalse($castMember->isActive);

        $castMember->activate();
        $this->assertTrue($castMember->isActive);

        $castMember->deactivate();
        $this->assertFalse($castMember->isActive);
    }
}
