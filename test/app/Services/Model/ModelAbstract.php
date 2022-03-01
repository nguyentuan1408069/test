<?php

namespace App\Services\Model;

use App\Exceptions\ModelNotFoundException;
use App\Services\Database\DB;
use App\Services\Pagination\Pagination;

abstract class ModelAbstract
{
    protected $db;

    protected $attributes = [];

    protected $fillable = [];

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    protected function primaryKey(): string
    {
        return 'id';
    }

    /**
     * Set the attribute with value for model
     *
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Set multiple attributes by passing array with key value pair
     *
     * @param array $data
     */
    public function setAttributes(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    public function getAttribute(string $key)
    {
        return array_get($this->attributes, $key);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * From the $fillable property, we can safely set which value can be used to insert to database
     *
     * @param array $data
     * @return array
     */
    protected function getFillableData(array $data): array
    {
        if (0 === count($this->fillable)) {
            return $data;
        }

        return collect($data)->filter(function ($value, $key) {
            return in_array($key, $this->fillable);
        })->toArray();
    }

    /**
     * Find the model by the given id
     *
     * @param $id
     * @return $this
     * @throws \App\Exceptions\ModelNotFoundException
     */
    public function find($id): ModelAbstract
    {
        $result = $this->db->findByColumnValue(
            $this->getTable(),
            $this->primaryKey(),
            $id
        );

        if (! $result) {
            throw new ModelNotFoundException();
        }

        $this->setAttributes($result);

        return $this;
    }

    /**
     * Find the model by the given conditions
     *
     * @param $data
     * @return $this
     */
    public function findAllBy($data): ModelAbstract
    {
        $result = $this->db->findAllByColumnValue(
            $this->getTable(),
            $data
        );

        $this->setAttributes($result);

        return $this;
    }

    /**
     * Get the list of models with pagination format
     *
     * @param int $page
     * @param int $perPage
     * @param array $orderBy
     * @return \App\Services\Pagination\Pagination
     */
    public function paginate(int $page = 1, int $perPage = 10, array $orderBy): Pagination
    {
        return $this->db->paginate($this->getTable(), $page, $perPage, $orderBy);
    }

    /**
     * Insert new model to database when return new created model
     *
     * @param array $attributes
     * @return $this
     * @throws \App\Exceptions\ModelNotFoundException
     */
    public function create(array $attributes): ModelAbstract
    {
        $newModelId = $this->db->insert(
            $this->getTable(),
            $this->getFillableData($attributes)
        );

        return $this->find($newModelId);
    }

    /**
     * Update model data by the given value
     *
     * @param array $data
     * @return $this
     */
    public function update(array $data): ModelAbstract
    {
        $this->db->update(
            $this->getTable(),
            $this->getAttribute('id'),
            $this->getFillableData($data)
        );

        $this->setAttributes(array_merge($this->toArray(), $data));

        return $this;
    }

    public function delete()
    {
        $this->db->deleteByColumnValue(
            $this->getTable(),
            'id',
            $this->getAttribute('id')
        );
    }

    /**
     * Get the database table name of model
     *
     * @return string
     */
    abstract protected function getTable(): string;
}