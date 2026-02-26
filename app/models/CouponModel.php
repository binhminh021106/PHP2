<?php

class CouponModel extends Model
{
    private $table = 'coupons';

    // Đã cập nhật để hỗ trợ Tìm kiếm và Phân trang
    public function index($search = '', $limit = 5, $offset = 0)
    {
        $sql = "SELECT * FROM $this->table WHERE deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND code LIKE :search";
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

    // Đếm tổng số bản ghi
    public function getTotalCoupons($search = '')
    {
        $sql = "SELECT COUNT(id) as total FROM $this->table WHERE deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND code LIKE :search";
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

    // Hàm kiểm tra trùng lặp mã Code
    public function checkCodeExists($code, $ignoreId = null)
    {
        $sql = "SELECT id FROM $this->table WHERE code = :code AND deleted_at IS NULL";
        $params = ['code' => $code];

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