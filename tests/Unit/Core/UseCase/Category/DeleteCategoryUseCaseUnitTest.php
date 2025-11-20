<?php

namespace Tests\Unit\Core\UseCase\Category;

use Core\Application\DTO\Input\Category\DeleteCategoryInputDto;
use Core\Application\DTO\Output\Category\DeleteCategoryOutputDto;
use Core\Application\UseCase\Category\DeleteCategoryUseCase;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteCategoryUseCaseUnitTest extends TestCase
{
    public function testDeleteCategory()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->andReturn(true);
        $useCase = new DeleteCategoryUseCase($mockRepository);
        $deleteCategoryInputDto = Mockery::mock(DeleteCategoryInputDto::class, [$id]);
        $responseUseCase = $useCase->execute($deleteCategoryInputDto);
        $this->assertInstanceOf(DeleteCategoryOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('delete')->andReturn(true);
        $useCase = new DeleteCategoryUseCase($spy);
        $deleteCategoryInputDto = new DeleteCategoryInputDto(id: $id);
        $responseUseCase = $useCase->execute($deleteCategoryInputDto);
        $spy->shouldHaveReceived('delete');
        Mockery::close();
    }

    public function testDeleteFalse()
    {
        $id = (string) Uuid::uuid4()->toString();
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->andReturn(false);
        $useCase = new DeleteCategoryUseCase($mockRepository);
        $deleteCategoryInputDto = Mockery::mock(DeleteCategoryInputDto::class, [$id]);
        $responseUseCase = $useCase->execute($deleteCategoryInputDto);
        $this->assertInstanceOf(DeleteCategoryOutputDto::class, $responseUseCase);
        $this->assertFalse($responseUseCase->success);

     
    }

    protected function tearDown(): void
    {
        
        Mockery::close();
        parent::tearDown();
        
    }
}
