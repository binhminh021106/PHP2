<?php

class CouponModel extends Model
{
    private $table = 'coupons';

    public function index()
    {
        $sql = "SELECT * FROM $this->table WHERE deleted_at IS NULL ORDER BY id DESC";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data = [])
    {
        $sql = "INSERT INTO $this->table (code, type, value, quantity, status, expired_at) 
                VALUES (:code, :type, :value, :quantity, :status, :expired_at)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'code' => $data['code'],
            'type' => $data['type'],
            'value' => $data['value'],
            'quantity' => $data['quantity'],
            'status' => $data['status'],
            'expired_at' => $data['expired_at']
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
        $sql = "UPDATE $this->table 
                SET code = :code, type = :type, value = :value, quantity = :quantity, 
                    status = :status, expired_at = :expired_at, updated_at = :updated_at 
                WHERE id = :id AND deleted_at IS NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'code' => $data['code'],
            'type' => $data['type'],
            'value' => $data['value'],
            'quantity' => $data['quantity'],
            'status' => $data['status'],
            'expired_at' => $data['expired_at'],
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