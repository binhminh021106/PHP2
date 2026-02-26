<?php

class UserModel extends Model
{
    private $table = 'users';

    // Đã cập nhật để hỗ trợ Tìm kiếm và Phân trang
    public function index($search = '', $limit = 5, $offset = 0)
    {
        $sql = "SELECT * FROM $this->table WHERE deleted_at is NULL";
        $params = [];

        if (!empty($search)) {
            // Tìm kiếm theo tên, email hoặc số điện thoại
            $sql .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
        
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số bản ghi (dùng cho tính toán phân trang)
    public function getTotalUsers($search = '')
    {
        $sql = "SELECT COUNT(id) as total FROM $this->table WHERE deleted_at is NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function create($data = [])
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO $this->table (name, phone, email, password, address, avatar_url, status, role) VALUES (:name, :phone, :email, :password, :address, :avatar_url, :status, :role)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'avatar_url' => $data['avatar_url'],
            'address' => $data['address'],
            'status' => $data['status'],
            'role' => $data['role'],
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

        $sql = "UPDATE $this->table SET name = :name, phone = :phone, email = :email, password = :password, address = :address, avatar_url = :avatar_url, status = :status, role = :role, updated_at = :updated_at WHERE id = :id AND deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'address' => $data['address'],
            'avatar_url' => $data['avatar_url'],
            'status' => $data['status'],
            'role' => $data['role'],
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

    public function checkEmailExists($email, $excludeId = null)
    {
        $sql = "SELECT id FROM $this->table WHERE email = :email AND deleted_at is NULL";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);

        $params = ['email' => $email];
        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function checkPhoneExists($phone, $excludeId = null)
    {
        $sql = "SELECT id FROM $this->table WHERE phone = :phone AND deleted_at is NULL";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);

        $params = ['phone' => $phone];
        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
}