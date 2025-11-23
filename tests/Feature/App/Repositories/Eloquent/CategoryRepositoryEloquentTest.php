<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category as CategoryModel;
use Tests\TestCase;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\PaginationInterface;

class CategoryRepositoryEloquentTest extends TestCase
{

    protected $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepositoryEloquent(new CategoryModel());
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testInsert()
    {
        $entity = new EntityCategory(name: 'Category 01', description: 'Category 01 description');
        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', [
            'id' => $response->id,
            'name' => $response->name,
            'description' => $response->description,
            'is_active' => $response->isActive,
            'created_at' => $response->createdAt,
        ]);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('Category 01', $response->name);
        $this->assertEquals('Category 01 description', $response->description);
    }

    public function testFindById()
    {
        $category = CategoryModel::factory()->create();
        $response = $this->repository->findById($category->id);
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', [
            'id' => $response->id,
        ]);
    }
    public function testFindByIdNotFound()
    {
        try {
            $this->repository->findById('fake_id');

            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testFindAll()
    {
        $categories = CategoryModel::factory()->count(10)->create();
        $response = $this->repository->findAll();
        $this->assertEquals(count($categories), count($response));
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertIsArray($response);
        $this->assertDatabaseHas('categories', [
            'id' => $response[0]['id'],
            'name' => $response[0]['name'],
            'description' => $response[0]['description'],
        ]);
    }

    public function testPaginate()
    {
        CategoryModel::factory()->count(100)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
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
        CategoryModel::factory()->count(0)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
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
            $category = new EntityCategory(name: 'Category 01');
            $this->repository->update($category);
            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testUpdate()
    {
        $categoryDb = CategoryModel::factory()->create();
        $category = new EntityCategory(
            id: $categoryDb->id,
            name: 'Category Updated',
            description: 'Category Updated description'
        );

        $response = $this->repository->update($category);
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', [
            'id' => $response->id,
            'name' => $response->name,
            'description' => $response->description,
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
        $categoryDb = CategoryModel::factory()->create();
        $response = $this->repository->delete($categoryDb->id);
        $this->assertTrue($response);
        $this->assertSoftDeleted('categories', [
            'id' => $categoryDb->id,
        ]);
    }
}
