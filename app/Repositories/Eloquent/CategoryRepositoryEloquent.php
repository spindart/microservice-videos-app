<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\Presenters\PaginatorPresenter;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;

class CategoryRepositoryEloquent implements CategoryRepositoryInterface
{
    public function __construct(
        protected Model $model
    ) {}
    public function insert(EntityCategory $entity): EntityCategory
    {
        $category = $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'description' => $entity->description,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);
        return $this->toCategory($category);
    }

    public function findById(string $id): EntityCategory
    {
        $category = $this->model->find($id);
        if (!$category) {
            throw new EntityNotFoundException('Category not found');
        }
        return $this->toCategory($category);
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        return $this->model->where(function ($query) use ($filter) {
            if ($filter) {
                $query->where('name', 'like', "%{$filter}%")
                    ->orWhere('description', 'like', "%{$filter}%");
            }
        })->orderBy($order, 'DESC')->get()->toArray();
    }

    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $perPage = 10): PaginationInterface
    {
        $query = $this->model;
        if ($filter) {
            $query->where('name', 'like', "%{$filter}%")
                ->orWhere('description', 'like', "%{$filter}%");
        }
        $query->orderBy($order, 'DESC');
        $paginator = $query->paginate($perPage);
        return new PaginatorPresenter($paginator);
    }

    public function update(EntityCategory $entity): EntityCategory
    {
        $category = $this->model->find($entity->id);
        if (!$category) {
            throw new EntityNotFoundException('Category not found');
        }
        $category->update([
            'name' => $entity->name,
            'description' => $entity->description,
            'is_active' => $entity->isActive,
        ]);
        $category->refresh();
        return $this->toCategory($category);
    }

    public function delete(string $id): bool
    {
        $category = $this->model->find($id);
        if (!$category) {
            throw new EntityNotFoundException('Category not found');
        }
        return $category->delete();
    }

    public function toCategory(object $model): EntityCategory
    {
        $entity = new EntityCategory(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            createdAt: $model->created_at,
        );

        ((bool) $model->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }
}
