<?php

class UserModel extends Model
{
    private $table = 'users';

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
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO $this->table (name, phone, email, password, avatar_url, status) VALUES (:name, :phone, :email, :password, :avatar_url, :status)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'avatar_url' => $data['avatar_url'],
            'status' => $data['status'],
        ]);
    }

    public function show($id)
    {
        $Sql = "SELECT * FROM $this->table WHERE id = :id AND deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($Sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data = [])
    {
        $now = date('Y-m-d H:i:s');
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "UPDATE $this->table SET name = :name, phone = :phone, email = :email, password = :password, avatar_url = :avatar_url, status = :status, updated_at = :updated_at WHERE id = :id AND deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'avatar_url' => $data['avatar_url'],
            'status' => $data['status'],
            'updated_at' => $now,
            'id' => $id
        ]);
    }

    public function delete($id)
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
