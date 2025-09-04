<?php

namespace Modules\Core\Infrastructure\Repositories;

use CodeIgniter\Model;
use Modules\Core\Domain\Entities\BaseEntity;

abstract class BaseRepository
{
    protected Model $model;
    protected string $entityClass;
    protected string $modelClass;

    public function __construct()
    {
        $this->model = new $this->modelClass();
    }

    public function find(int $id): ?BaseEntity
    {
        $model = $this->model->find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(): array
    {
        $models = $this->model->findAll();
        return array_map([$this, 'toEntity'], $models);
    }

    public function create(array $data): BaseEntity
    {
        $id = $this->model->insert($data);
        return $this->find($id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function where(string $field, $value): array
    {
        $models = $this->model->where($field, $value)->findAll();
        return array_map([$this, 'toEntity'], $models);
    }

    public function first(): ?BaseEntity
    {
        $model = $this->model->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function count(): int
    {
        return $this->model->countAllResults();
    }

    public function paginate(int $perPage = 10, int $page = 1): array
    {
        $result = $this->model->paginate($perPage, 'default', $page);

        return [
            'data' => array_map([$this, 'toEntity'], $result['data']),
            'pager' => $result['pager']
        ];
    }

    protected function toEntity($model): BaseEntity
    {
        $entity = new $this->entityClass();

        if (is_array($model)) {
            $entity->fill($model);
        } else {
            $entity->fill($model->toArray());
        }

        return $entity;
    }

    protected function toArray(BaseEntity $entity): array
    {
        return $entity->toArray();
    }
}
