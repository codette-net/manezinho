<?php

namespace CMSOJ\Core;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';

    public function db()
    {
        return Database::connect();
    }

    public function all()
    {
        return $this->db()->query("SELECT * FROM {$this->table}")->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $values  = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db()->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");
        $stmt->execute(array_values($data));

        return $this->db()->lastInsertId();
    }

    public function update($id, array $data)
    {
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));

        $stmt = $this->db()->prepare("UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?");
        return $stmt->execute([...array_values($data), $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
}
