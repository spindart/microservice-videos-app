<?php

namespace Tests\Unit\UseCase;

use Core\Application\DTO\Category\CategoryCreateInputDto;
use Core\Application\DTO\Category\CategoryCreateOutputDto;
use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Application\UseCase\Category\CreateCategoryUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateCategoryUseCaseUnitTest extends TestCase
{

    public function testCreateNewCategory()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $mockEntity = new Category(
            id: $uuid,
            name: 'Test Category',
            description: 'Test Description',
            isActive: true
        );
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('id')->andReturn($uuid);
        $mockRepository->shouldReceive('insert')->andReturn($mockEntity);
        $useCase = new CreateCategoryUseCase($mockRepository);
        $categoryCreateInputDto = new CategoryCreateInputDto(
            name: 'Test Category',
            description: 'Test Description',
            isActive: true
        );
        $responseUseCase = $useCase->execute($categoryCreateInputDto);
        $this->assertInstanceOf(CategoryCreateOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);
        $this->assertEquals('Test Category', $responseUseCase->name);
        $this->assertEquals('Test Description', $responseUseCase->description);
        $this->assertTrue($responseUseCase->is_active);
        $this->assertTrue(true);
        Mockery::close();
    }
}
