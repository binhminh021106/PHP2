<?php

class BrandModel extends Model
{
    private $table = 'brands';

    public function index()
    {
        $sql = "SELECT * FROM $this->table WHERE deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data = [])
    {
        $sql = "INSERT INTO $this->table (name, image, description) VALUES (:name, :image, :description)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'image' => $data['image'],
            'description' => $data['description'],
        ]);
    }

    public function show($id) 
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id AND deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data = [])
    {
        $sql = "UPDATE $this->table SET name = :name, image = :image, description = :description WHERE id = :id AND deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'image' => $data['image'],
            'description' => $data['description'],
            'id' => $id
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