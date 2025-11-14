<?php

namespace Tests\Unit\UseCase\Category;

use Core\Application\DTO\Input\Category\ListCategoriesInputDto;
use Core\Application\DTO\Output\Category\ListCategoriesOutputDto;
use Core\Application\UseCase\Category\ListCategoriesUseCase;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class ListCategoriesUseCaseUnitTest extends TestCase
{
    public function testListCategoriesEmpty()
    {

        $mockPagination = $this->mockPagination();
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->andReturn($mockPagination);

        $useCase = new ListCategoriesUseCase($mockRepository);
        $mockInputDto = new ListCategoriesInputDto(filter: '', order: 'DESC', page: 1, perPage: 10);
        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(ListCategoriesOutputDto::class, $responseUseCase);
        $this->assertCount(0, $responseUseCase->items);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('paginate')->andReturn($mockPagination);
        $useCase = new ListCategoriesUseCase($spy);
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('paginate');
    }
    public function testListCategories()
    {

        $register = new stdClass();
        $register->id = (string) Uuid::uuid4()->toString();
        $register->name = 'Test Category';
        $register->description = 'Test Description';
        $register->is_active = true;
        $register->created_at = new DateTime();
        $register->updated_at = null;
        $register->deleted_at = null;


        $mockPagination = $this->mockPagination(items: [$register]);
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->andReturn($mockPagination);

        $useCase = new ListCategoriesUseCase($mockRepository);
        $mockInputDto = new ListCategoriesInputDto(filter: '', order: 'DESC', page: 1, perPage: 10);
        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(stdClass::class, $responseUseCase->items[0]);
        $this->assertEquals('Test Category', $responseUseCase->items[0]->name);
        $this->assertEquals('Test Description', $responseUseCase->items[0]->description);
        $this->assertTrue($responseUseCase->items[0]->is_active);
        $this->assertInstanceOf(DateTime::class, $responseUseCase->items[0]->created_at);
        $this->assertNull($responseUseCase->items[0]->updated_at);
        $this->assertNull($responseUseCase->items[0]->deleted_at);

        $this->assertInstanceOf(ListCategoriesOutputDto::class, $responseUseCase);
        $this->assertCount(1, $responseUseCase->items);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('paginate')->andReturn($mockPagination);
        $useCase = new ListCategoriesUseCase($spy);
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('paginate');
    }

    protected function mockPagination(
        array $items = [],
        int  $total = 0,
        int  $currentPage = 0,
        int  $perPage = 0,
        int  $lastPage = 0,
        int  $from = 0,
        int  $to = 0,
        int  $firstPage = 0,
        int  $nextPage = 0,
        int  $previousPage = 0
    ) {
        $this->mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $this->mockPagination->shouldReceive('items')->andReturn($items);
        $this->mockPagination->shouldReceive('total')->andReturn($total);
        $this->mockPagination->shouldReceive('currentPage')->andReturn($currentPage);
        $this->mockPagination->shouldReceive('perPage')->andReturn($perPage);
        $this->mockPagination->shouldReceive('lastPage')->andReturn($lastPage);
        $this->mockPagination->shouldReceive('from')->andReturn($from);
        $this->mockPagination->shouldReceive('to')->andReturn($to);
        $this->mockPagination->shouldReceive('firstPage')->andReturn($firstPage);
        $this->mockPagination->shouldReceive('nextPage')->andReturn($nextPage);
        $this->mockPagination->shouldReceive('previousPage')->andReturn($previousPage);

        return $this->mockPagination;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
