<?php

class WishlistModel extends Model
{
    private $table = 'wishlists';
    public function index()
    {
        $sql = "SELECT * FROM $this->table";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data = [])
    {
        $sql = "INSERT INTO $this->table (product_id, user_id) VALUES (:product_id, :user_id)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'product_id' => $data['product_id'],
            'user_id' => $data['user_id']
        ]);
    }

    public function update($id, $data = [])
    {
        $now = date('Y-m-d H:i:s');

        $sql = "UPDATE $this->table SET product_id = :product_id, user_id = :user_id, updated_at = :updated_at WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'product_id' => $data['product_id'],
            'user_id' => $data['user_id'],
            'updated_at' => $now,
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'id' => $id
        ]);
    }
}
