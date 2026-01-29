<?php

class CategoryModel extends Model
{
    private $table = 'categories';

    public function index()
    {
        $sql = "SELECT * FROM $this->table WHERE deleted_at IS NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data = [])
    {
        $sql = "INSERT INTO $this->table (name, description, icon, status) VALUES (:name, :description, :icon, :status)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'icon' => $data['icon'],
            'status' => $data['status']
        ]);
    }

    public function show($id)
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id AND deleted_at IS NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data = [])
    {
        $now = date('Y-m-d H:i:s');

        $sql = "UPDATE $this->table SET name = :name, description = :description, icon = :icon, status = :status, updated_at = :updated_at WHERE id = :id AND deleted_at IS NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'icon' => $data['icon'],
            'status' => $data['status'],
            'updated_at' => $now,
            'id' => $id,
        ]);
    }

    public function destroy($id)
    {
        $now = date('Y-m-d H:i:s');

        $sql = "UPDATE $this->table SET deleted_at = :deleted_at WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'deleted_at' => $now
        ]);
    }
}
