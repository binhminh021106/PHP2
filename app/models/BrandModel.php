<?php

class BrandModel extends \Model
{
    private $table = 'brands';

    // Đã cập nhật để hỗ trợ Tìm kiếm và Phân trang
    public function index($search = '', $limit = 5, $offset = 0)
    {
        $sql = "SELECT * FROM $this->table WHERE deleted_at is NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND name LIKE :search";
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
    public function getTotalBrands($search = '')
    {
        $sql = "SELECT COUNT(id) as total FROM $this->table WHERE deleted_at is NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND name LIKE :search";
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

    // Hàm kiểm tra tên thương hiệu có bị trùng không
    public function checkNameExists($name, $ignoreId = null)
    {
        $sql = "SELECT id FROM $this->table WHERE name = :name AND deleted_at is NULL";
        $params = ['name' => $name];

        if ($ignoreId) {
            $sql .= " AND id != :ignore_id";
            $params['ignore_id'] = $ignoreId;
        }

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch() ? true : false;
    }

    public function create($data = [])
    {
        $sql = "INSERT INTO $this->table (name, image, description, status) VALUES (:name, :image, :description, :status)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'image' => $data['image'],
            'description' => $data['description'],
            'status' => $data['status']
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
        $sql = "UPDATE $this->table SET name = :name, image = :image, description = :description, status = :status WHERE id = :id AND deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'image' => $data['image'],
            'description' => $data['description'],
            'status' => $data['status'],
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