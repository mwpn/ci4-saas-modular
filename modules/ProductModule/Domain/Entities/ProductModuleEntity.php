<?php

namespace Modules\ProductModule\Domain\Entities;

class ProductModuleEntity
{
    protected $id;
    protected $name;
    protected $description;
    protected $created_at;
    protected $updated_at;

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; return $this; }
    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; return $this; }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}