<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\Presenters\PaginatorPresenter;
use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;

class GenreRepositoryEloquent implements GenreRepositoryInterface
{
    public function __construct(
        protected Model $model
    ) {}
    public function insert(EntityGenre $entity): EntityGenre
    {
        $genre = $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);
        return $this->toGenre($genre);
    }

    public function findById(string $id): EntityGenre
    {
        $genre = $this->model->find($id);
        if (!$genre) {
            throw new EntityNotFoundException('Genre not found');
        }
        return $this->toGenre($genre);
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

    public function update(EntityGenre $entity): EntityGenre
    {
        $genre = $this->model->find($entity->id);
        if (!$genre) {
            throw new EntityNotFoundException('Genre not found');
        }
        $genre->update([
            'name' => $entity->name,
            'is_active' => $entity->isActive,
        ]);
        $genre->refresh();
        return $this->toGenre($genre);
    }

    public function delete(string $id): bool
    {
        $genre = $this->model->find($id);
        if (!$genre) {
            throw new EntityNotFoundException('Genre not found');
        }
        return $genre->delete();
    }

    public function toGenre(object $model): EntityGenre
    {
        $entity = new EntityGenre(
            id: $model->id,
            name: $model->name,
            createdAt: $model->created_at,
        );

        ((bool) $model->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }
}
