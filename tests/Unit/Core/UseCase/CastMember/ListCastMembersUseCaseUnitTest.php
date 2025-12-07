<?php

namespace Tests\Unit\Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Application\DTO\Input\CastMember\ListCastMembersInputDto;
use Core\Application\DTO\Output\CastMember\ListCastMembersOutputDto;
use Core\Application\UseCase\CastMember\ListCastMembersUseCase;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\PaginationInterface;
use DateTime;
use Mockery;
use Ramsey\Uuid\Nonstandard\Uuid;
use stdClass;
use Tests\TestCase;

class ListCastMembersUseCaseUnitTest extends TestCase
{


    public function testCastMembersListEmpty()
    {

        $mockPagination = $this->mockPagination();
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->once()->andReturn($mockPagination);

        $useCase = new ListCastMembersUseCase($mockRepository);
        $mockInputDto = Mockery::mock(ListCastMembersInputDto::class, ['', 'DESC', 1, 10]);

        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertCount(0, $responseUseCase->items);
        $this->assertInstanceOf(ListCastMembersOutputDto::class, $responseUseCase);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $spy->shouldReceive('paginate')->andReturn($mockPagination);
        $useCase = new ListCastMembersUseCase($spy);
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('paginate');
    }

    public function testCastMembersList()
    {
        $register = new stdClass();
        $register->id = (string) Uuid::uuid4()->toString();
        $register->name = 'Test Cast Member';
        $register->type = CastMemberType::ACTOR;
        $register->is_active = true;
        $register->created_at = new DateTime();
        $register->updated_at = null;
        $register->deleted_at = null;


        $mockPagination = $this->mockPagination(items: [$register]);
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->once()->andReturn($mockPagination);

        $useCase = new ListCastMembersUseCase($mockRepository);
        $mockInputDto = Mockery::mock(ListCastMembersInputDto::class, ['', 'DESC', 1, 10]);

        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(stdClass::class, $responseUseCase->items[0]);
        $this->assertEquals('Test Cast Member', $responseUseCase->items[0]->name);
        $this->assertEquals(CastMemberType::ACTOR, $responseUseCase->items[0]->type);
        $this->assertTrue($responseUseCase->items[0]->is_active);
        $this->assertInstanceOf(DateTime::class, $responseUseCase->items[0]->created_at);
        $this->assertNull($responseUseCase->items[0]->updated_at);
        $this->assertNull($responseUseCase->items[0]->deleted_at);

        $this->assertInstanceOf(ListCastMembersOutputDto::class, $responseUseCase);
        $this->assertCount(1, $responseUseCase->items);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $spy->shouldReceive('paginate')->andReturn($mockPagination);
        $useCase = new ListCastMembersUseCase($spy);
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
