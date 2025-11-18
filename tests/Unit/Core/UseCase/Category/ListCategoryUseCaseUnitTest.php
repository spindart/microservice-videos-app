<?php

namespace Tests\Unit\Core\UseCase\Category;

use Core\Application\DTO\Input\Category\ListCategoryInputDto;
use Core\Application\DTO\Output\Category\ListCategoryOutputDto;
use Core\Application\UseCase\Category\ListCategoryUseCase;
use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use stdClass;

class ListCategoryUseCaseUnitTest extends TestCase
{
    public function testGetById()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockEntity = new Category(
            id: $id,
            name: 'Test Category',
            description: 'Test Description',
            isActive: true
        );
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->with($id)->andReturn($mockEntity);
        $mockRepository->shouldReceive('createdAt')->andReturn($mockEntity->createdAt());
        $mockRepository->shouldReceive('id')->andReturn($id);
        $useCase = new ListCategoryUseCase($mockRepository);
        $listCategoryInputDto = new ListCategoryInputDto(id: $id);
        $responseUseCase = $useCase->execute($listCategoryInputDto);

        $this->assertTrue(true);
        $this->assertInstanceOf(ListCategoryOutputDto::class, $responseUseCase);
        $this->assertEquals($id, $responseUseCase->id);
        $this->assertEquals('Test Category', $responseUseCase->name);
        $this->assertEquals('Test Description', $responseUseCase->description);
        $this->assertTrue($responseUseCase->is_active);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('findById')->with($id)->andReturn($mockEntity);
        $useCase = new ListCategoryUseCase($spy);
        $listCategoryInputDto = new ListCategoryInputDto(id: $id);
        $responseUseCase = $useCase->execute($listCategoryInputDto);
        $spy->shouldHaveReceived('findById');
        Mockery::close();
    }
}
