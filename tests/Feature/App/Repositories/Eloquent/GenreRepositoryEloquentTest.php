<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Genre as GenreModel;
use App\Models\Category as CategoryModel;
use Tests\TestCase;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class GenreRepositoryEloquentTest extends TestCase
{

    protected $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreRepositoryEloquent(new GenreModel());
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testInsert()
    {
        $entity = new EntityGenre(name: 'Genre 01');
        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertDatabaseHas('genres', [
            'id' => $response->id,
            'name' => $response->name,
            'is_active' => $response->isActive,
            'created_at' => $response->createdAt,
        ]);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('Genre 01', $response->name);
    }
    public function testInsertDeactivate()
    {
        $entity = new EntityGenre(name: 'Genre 01');
        $entity->deactivate();
        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertDatabaseHas('genres', [
            'id' => $response->id,
            'name' => $response->name,
            'is_active' => false,
            'created_at' => $response->createdAt,
        ]);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('Genre 01', $response->name);
        $this->assertFalse($response->isActive);
    }

    public function testInsertWithRelationships()
    {
        $categories = CategoryModel::factory(4)->create();
        $entity = new EntityGenre(
            name: 'Genre 01',
        );
        foreach ($categories as $category) {
            $entity->addCategory($category->id);
        }

        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertDatabaseHas('genres', [
            'id' => $response->id,
            'name' => $response->name,
            'is_active' => $response->isActive,
            'created_at' => $response->createdAt,
        ]);
        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $response->id,
            'category_id' => $categories->first()->id,
        ]);
        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $response->id,
            'category_id' => $categories->last()->id,
        ]);
        $this->assertDatabaseCount('category_genre', 4);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('Genre 01', $response->name);
    }


    public function testFindById()
    {
        $genre = GenreModel::factory()->create();
        $response = $this->repository->findById($genre->id);
        $this->assertDatabaseHas('genres', [
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
        $genres = GenreModel::factory()->count(10)->create();
        $response = $this->repository->findAll();
        $this->assertEquals(count($genres), count($response));
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertIsArray($response);
        $this->assertDatabaseHas('genres', [
            'id' => $response[0]['id'],
            'name' => $response[0]['name'],
        ]);
    }

    public function testFindAllWithFilter()
    {
        GenreModel::factory()->create(['name' => 'Genre Test']);
        GenreModel::factory()->count(10)->create();
        $response = $this->repository->findAll(filter: 'Test');
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertIsArray($response);
        $this->assertEquals(1, count($response));
        $this->assertDatabaseHas('genres', [
            'id' => $response[0]['id'],
            'name' => $response[0]['name'],
        ]);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertIsArray($response);
        $this->assertEquals(0, count($response));
    }

    public function testPaginate()
    {
        GenreModel::factory()->count(100)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
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
        GenreModel::factory()->count(0)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
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
            $genre = new EntityGenre(name: 'Genre 01');
            $this->repository->update($genre);
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testUpdate()
    {
        $genreDb = GenreModel::factory()->create();
        $genre = new EntityGenre(
            id: new Uuid($genreDb->id),
            name: 'Genre Updated',
            isActive: $genreDb->is_active,
            createdAt: new DateTime($genreDb->created_at),
        );

        $response = $this->repository->update($genre);
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertDatabaseHas('genres', [
            'id' => $response->id,
            'name' => $response->name,
            'is_active' => $response->isActive,
        ]);
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
        $genreDb = GenreModel::factory()->create();
        $response = $this->repository->delete($genreDb->id);
        $this->assertTrue($response);
        $this->assertSoftDeleted('genres', [
            'id' => $genreDb->id,
        ]); 
    }
}
