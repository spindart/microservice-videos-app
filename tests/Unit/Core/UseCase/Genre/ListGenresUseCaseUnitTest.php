<?php

namespace Tests\Unit\Core\UseCase\Genre;

use Core\Application\DTO\Input\Genre\ListGenresInputDto;
use Core\Application\DTO\Output\Genre\ListGenresOutputDto;
use Core\Application\UseCase\Genre\ListGenresUseCase;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class ListGenresUseCaseUnitTest extends TestCase
{
    public function testListGenresEmpty()
    {
        $mockPagination = $this->mockPagination();
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->once()->andReturn($mockPagination);

        $useCase = new ListGenresUseCase($mockRepository);
        $mockInputDto = Mockery::mock(ListGenresInputDto::class, ['Test',  'DESC', 1, 10]);
        // $mockInputDto = new ListGenresInputDto(filter: '', order: 'DESC', page: 1, perPage: 10);
        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(ListGenresOutputDto::class, $responseUseCase);
        $this->assertCount(0, $responseUseCase->items);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive('paginate')->andReturn($mockPagination);
        $useCase = new ListGenresUseCase($spy);
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('paginate');
    }
    public function testListGenres()
    {

        $register = new stdClass();
        $register->id = (string) Uuid::uuid4()->toString();
        $register->name = 'Test Genre';
        $register->description = 'Test Description';
        $register->is_active = true;
        $register->created_at = new DateTime();
        $register->updated_at = null;
        $register->deleted_at = null;


        $mockPagination = $this->mockPagination(items: [$register]);
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->once()->andReturn($mockPagination);

        $useCase = new ListGenresUseCase($mockRepository);
        $mockInputDto = Mockery::mock(ListGenresInputDto::class, ['Test',  'DESC', 1, 10]);
        // $mockInputDto = new ListGenresInputDto(filter: '', order: 'DESC', page: 1, perPage: 10);
        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(stdClass::class, $responseUseCase->items[0]);
        $this->assertEquals('Test Genre', $responseUseCase->items[0]->name);
        $this->assertEquals('Test Description', $responseUseCase->items[0]->description);
        $this->assertTrue($responseUseCase->items[0]->is_active);
        $this->assertInstanceOf(DateTime::class, $responseUseCase->items[0]->created_at);
        $this->assertNull($responseUseCase->items[0]->updated_at);
        $this->assertNull($responseUseCase->items[0]->deleted_at);

        $this->assertInstanceOf(ListGenresOutputDto::class, $responseUseCase);
        $this->assertCount(1, $responseUseCase->items);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive('paginate')->once()->andReturn($mockPagination);
        $useCase = new ListGenresUseCase($spy);
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('paginate')->with('Test', 'DESC', 1, 10);
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
