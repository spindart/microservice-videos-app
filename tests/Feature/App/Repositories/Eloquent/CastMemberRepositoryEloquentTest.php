<?php

namespace Test\Feature\App\Repositories\Eloquent;

use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\Domain\Entity\CastMember as EntityCasMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Tests\TestCase;

class CastMemberRepositoryEloquentTest extends TestCase
{

    protected $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberRepositoryEloquent(new CastMemberModel());
    }

    public function testInsert()
    {
        $entity = new EntityCasMember(name: 'Cast Member 01', type: CastMemberType::ACTOR);
        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCasMember::class, $response);
        $this->assertDatabaseHas('cast_members', [
            'id' => $response->id,
            'name' => $response->name,
            'type' => $response->type,
            'is_active' => $response->isActive,
            'created_at' => $response->createdAt,
        ]);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('Cast Member 01', $response->name);
        $this->assertEquals(CastMemberType::ACTOR, $response->type);
    }

    public function testInsertDeactivate()
    {
        $entity = new EntityCasMember(name: 'CastMember 01');
        $entity->deactivate();
        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCasMember::class, $response);
        $this->assertDatabaseHas('cast_members', [
            'id' => $response->id,
            'name' => $response->name,
            'is_active' => false,
            'created_at' => $response->createdAt,
        ]);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('CastMember 01', $response->name);
        $this->assertFalse($response->isActive);
    }



    public function testFindById()
    {
        $castMember = CastMemberModel::factory()->create();
        $response = $this->repository->findById($castMember->id);
        $this->assertDatabaseHas('cast_members', [
            'id' => $response->id,
        ]);
    }
    public function testFindByIdNotFound()
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->findById('fake_id');
    }

    public function testFindAll()
    {
        $cast_members = CastMemberModel::factory()->count(10)->create();
        $response = $this->repository->findAll();
        $this->assertEquals(count($cast_members), count($response));
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertIsArray($response);
        $this->assertDatabaseHas('cast_members', [
            'id' => $response[0]['id'],
            'name' => $response[0]['name'],
            'type' => $response[0]['type']
        ]);
    }

    public function testFindAllWithFilter()
    {
        CastMemberModel::factory()->create(['name' => 'CastMember Test']);
        CastMemberModel::factory()->count(10)->create();
        $response = $this->repository->findAll(filter: 'Test');
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertIsArray($response);
        $this->assertEquals(1, count($response));
        $this->assertDatabaseHas('cast_members', [
            'id' => $response[0]['id'],
            'name' => $response[0]['name'],
        ]);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertIsArray($response);
        $this->assertEquals(0, count($response));
    }

    public function testPaginate()
    {
        CastMemberModel::factory()->count(100)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertEquals(10, count($response->items()));
        $this->assertEquals(100, $response->total());
        $this->assertEquals(1, $response->currentPage());
        $this->assertEquals(10, $response->perPage());
        $this->assertEquals(10, $response->lastPage());
        $this->assertEquals(2, $response->nextPage());
        $this->assertEquals(0, $response->previousPage());
    }

    public function testPaginateEmpty()
    {
        CastMemberModel::factory()->count(0)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertEquals(0, count($response->items()));
        $this->assertEquals(0, $response->total());
        $this->assertEquals(1, $response->currentPage());
        $this->assertEquals(10, $response->perPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(0, $response->nextPage());
    }

    public function testUpdateIdNotFound()
    {

        try {
            $castMember = new EntityCasMember(name: 'CastMember 01');
            $this->repository->update($castMember);
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testUpdate()
    {
        $castMemberDb = CastMemberModel::factory()->create();
        $castMember = new EntityCasMember(
            id: new Uuid($castMemberDb->id),
            name: 'CastMember Updated',
            type: CastMemberType::DIRECTOR,
            isActive: $castMemberDb->is_active,
            createdAt: new DateTime($castMemberDb->created_at),
        );

        $response = $this->repository->update($castMember);
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCasMember::class, $response);
        $this->assertDatabaseHas('cast_members', [
            'id' => $response->id,
            'name' => $response->name,
            'type' => $response->type,
            'is_active' => $response->isActive,
        ]);

        $this->assertEquals(CastMemberType::DIRECTOR, $response->type);
    }

    public function testDeleteIdNotFound()
    {
        try {
            $this->repository->delete('fake_id');
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testDelete()
    {
        $castMemberDb = CastMemberModel::factory()->create();
        $response = $this->repository->delete($castMemberDb->id);
        $this->assertTrue($response);
        $this->assertSoftDeleted('cast_members', [
            'id' => $castMemberDb->id,
        ]);
    }
}
