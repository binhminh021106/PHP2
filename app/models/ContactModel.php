<?php

class ContactModel extends Model
{
    private $table = 'contacts';

    public function index()
    {
        $sql = "SELECT * FROM $this->table";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getContacts($search = '', $limit = 10, $offset = 0)
    {
        $conn = $this->connect();

        $sql = "SELECT * FROM $this->table";

        // Thêm điều kiện tìm kiếm nếu có
        if (!empty($search)) {
            $sql .= " WHERE fullname LIKE :search OR email LIKE :search OR title LIKE :search OR phone LIKE :search";
        }

        // Sắp xếp mới nhất lên đầu
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($sql);

        // Bind param tìm kiếm
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bindParam(':search', $searchTerm);
        }

        // Bind param phân trang (Bắt buộc dùng PARAM_INT cho limit/offset)
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số bản ghi (để tính số trang)
    public function countContacts($search = '')
    {
        $conn = $this->connect();
        $sql = "SELECT COUNT(*) as total FROM $this->table";

        if (!empty($search)) {
            $sql .= " WHERE fullname LIKE :search OR email LIKE :search OR title LIKE :search OR phone LIKE :search";
        }

        $stmt = $conn->prepare($sql);

        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bindParam(':search', $searchTerm);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function create($data = [])
    {
        $sql = "INSERT INTO $this->table (title, content, email, fullname, phone) VALUES (:title, :content, :email, :fullname, :phone)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'title' => $data['title'],
            'content' => $data['content'],
            'email' => $data['email'],
            'fullname' => $data['fullname'],
            'phone' => $data['phone']
        ]);
    }

    public function show($id)
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function destroy($id)
    {

        $sql = "DELETE FROM $this->table WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'id' => $id
        ]);
    }
}
