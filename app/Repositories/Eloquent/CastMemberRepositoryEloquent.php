<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\Presenters\PaginatorPresenter;
use Core\Domain\Entity\CastMember as EntityCastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use DateTime;

class CastMemberRepositoryEloquent implements CastMemberRepositoryInterface
{
    public function __construct(
        protected Model $model
    ) {}
    public function insert(EntityCastMember $entity): EntityCastMember
    {
        $castMember = $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'type' => $entity->type(),
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);

        return $this->toCastMember($castMember);
    }

    public function findById(string $id): EntityCastMember
    {
        $castMember = $this->model->find($id);
        if (!$castMember) {
            throw new EntityNotFoundException('CastMember not found');
        }
        return $this->toCastMember($castMember);
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

    public function update(EntityCastMember $entity): EntityCastMember
    {
        $castMember = $this->model->find($entity->id);
        if (!$castMember) {
            throw new EntityNotFoundException('CastMember not found');
        }
        $castMember->update([
            'name' => $entity->name,
            'type' => $entity->type()->value,
            'is_active' => $entity->isActive,
        ]);

        $castMember->refresh();
        return $this->toCastMember($castMember);
    }

    public function delete(string $id): bool
    {
        $castMember = $this->model->find($id);
        if (!$castMember) {
            throw new EntityNotFoundException('CastMember not found');
        }
        return $castMember->delete();
    }

    public function toCastMember(object $model): EntityCastMember
    {
        $type = $model->type instanceof CastMemberType 
            ? $model->type 
            : CastMemberType::tryFrom($model->type);

        $entity = new EntityCastMember(
            id: $model->id,
            name: $model->name,
            type: $type,
            createdAt: new DateTime($model->created_at),
        );

        ((bool) $model->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }
}
