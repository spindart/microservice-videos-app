<?php

namespace Tests\Unit\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class ModelTestCase extends \PHPUnit\Framework\TestCase
{
    abstract protected function model(): Model;
    abstract protected function traits(): array;
    abstract protected function fillables(): array;
    abstract protected function casts(): array;

    public function testIfUseTraits()
    {
        $traitsNeed = $this->traits();

        $traitsUsed = array_keys(class_uses($this->model()));

        $this->assertEquals(
            $traitsNeed,
            $traitsUsed
        );
    }

    public function testIncrementingIsFalse()
    {
        $this->assertFalse($this->model()->incrementing);
    }

    public function testFillableAttributes()
    {
        $fillableNeed = $this->fillables();

        $this->assertEquals(
            $fillableNeed,
            $this->model()->getFillable()
        );
    }

    public function testHasCastAttributes()
    {
        $castsNeed = $this->casts();

        $this->assertEquals(
            $castsNeed,
            $this->model()->getCasts()
        );
    }
}
