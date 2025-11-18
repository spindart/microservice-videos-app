<?php

namespace Tests\Unit\Core\UseCase\Category;

use Core\Application\DTO\Input\Category\CategoryUpdateInputDto;
use Core\Application\DTO\Output\Category\CategoryUpdateOutputDto;
use Core\Application\UseCase\Category\UpdateCategoryUseCase;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateCategoryUseCaseUnitTest extends TestCase
{
    public function testUpdateCategory()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockEntity = new EntityCategory(
            id: $id,
            name: 'Test Category',
            description: 'Test Description',
            isActive: true
        );

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->andReturn($mockEntity);
        $useCase = new UpdateCategoryUseCase($mockRepository);
        $categoryUpdateInputDto = new CategoryUpdateInputDto(
            id: $id,
            name: 'Test Category Updated',
            description: 'Test Description Updated',
            isActive: true
        );
        $responseUseCase = $useCase->execute($categoryUpdateInputDto);
        $this->assertInstanceOf(CategoryUpdateOutputDto::class, $responseUseCase);
        $this->assertEquals($id, $responseUseCase->id);
        $this->assertEquals('Test Category Updated', $responseUseCase->name);
        $this->assertEquals('Test Description Updated', $responseUseCase->description);
        $this->assertTrue($responseUseCase->is_active);
        $this->assertTrue(true);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('findById')->andReturn($mockEntity);
        $spy->shouldReceive('createdAt')->andReturn($mockEntity->createdAt());

        $spy->shouldReceive('update')->andReturn($mockEntity);
        $useCase = new UpdateCategoryUseCase($spy);
        $categoryUpdateInputDto = new CategoryUpdateInputDto(
            id: $id,
            name: 'Test Category Updated',
            description: 'Test Description Updated',
            isActive: true
        );
        $responseUseCase = $useCase->execute($categoryUpdateInputDto);
        $spy->shouldHaveReceived('findById');
        $spy->shouldHaveReceived('update');


        Mockery::close();
    }
}
