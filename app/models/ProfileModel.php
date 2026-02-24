<?php

class ProfileModel extends Model
{
    /**
     * Lấy thông tin chi tiết của User
     */
    public function getUserById($userId)
    {
        $conn = $this->connect();
        $stmt = $conn->prepare("SELECT id, name, email, phone, role, status FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật thông tin cá nhân cơ bản
     */
    public function updateUserInfo($userId, $name, $phone)
    {
        $conn = $this->connect();
        $stmt = $conn->prepare("UPDATE users SET name = :name, phone = :phone WHERE id = :id");
        return $stmt->execute([
            'name' => $name,
            'phone' => $phone,
            'id' => $userId
        ]);
    }

    /**
     * Cập nhật ảnh đại diện (avatar)
     */
    public function updateAvatar($userId, $fileName)
    {
        $conn = $this->connect();
        // Kiểm tra xem bảng users của bạn lưu ảnh ở cột 'avatar' hay 'picture'
        // Bạn có thể đổi tên cột dưới đây cho phù hợp với cấu trúc CSDL của bạn
        $stmt = $conn->prepare("UPDATE users SET avatar_url = :avatar WHERE id = :id");
        return $stmt->execute([
            'avatar' => $fileName,
            'id' => $userId
        ]);
    }

    /**
     * Lấy danh sách địa chỉ của User
     */
    public function getAddresses($userId)
    {
        $conn = $this->connect();
        $stmt = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC, id DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm địa chỉ mới
     */
    public function addAddress($userId, $fullname, $phone, $address, $isDefault = 0)
    {
        $conn = $this->connect();

        // Nếu set là mặc định, phải bỏ mặc định của các địa chỉ cũ
        if ($isDefault == 1) {
            $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :user_id")->execute(['user_id' => $userId]);
        } else {
            // Nếu chưa có địa chỉ nào, tự động set địa chỉ đầu tiên là mặc định
            $check = $conn->prepare("SELECT COUNT(*) FROM user_addresses WHERE user_id = :user_id");
            $check->execute(['user_id' => $userId]);
            if ($check->fetchColumn() == 0) {
                $isDefault = 1;
            }
        }

        $stmt = $conn->prepare("INSERT INTO user_addresses (user_id, fullname, phone, address, is_default) VALUES (:user_id, :fullname, :phone, :address, :is_default)");
        return $stmt->execute([
            'user_id' => $userId,
            'fullname' => $fullname,
            'phone' => $phone,
            'address' => $address,
            'is_default' => $isDefault
        ]);
    }

    /**
     * Đặt địa chỉ làm mặc định
     */
    public function setDefaultAddress($addressId, $userId)
    {
        $conn = $this->connect();
        // Bỏ mặc định tất cả
        $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :user_id")->execute(['user_id' => $userId]);
        
        // Set mặc định cho ID được chọn
        $stmt = $conn->prepare("UPDATE user_addresses SET is_default = 1 WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $addressId, 'user_id' => $userId]);
    }

    /**
     * Xóa địa chỉ
     */
    public function deleteAddress($addressId, $userId)
    {
        $conn = $this->connect();
        $stmt = $conn->prepare("DELETE FROM user_addresses WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $addressId, 'user_id' => $userId]);
    }
}