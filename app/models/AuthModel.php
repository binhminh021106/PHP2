<?php

class AuthModel extends Model
{
    private $table = 'users';

    public function findUserByEmail($email)
    {
        $sql = "SELECT * FROM $this->table WHERE email = :email AND deleted_at is NULL";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findUserByGoogleId($googleId)
    {
        $sql = "SELECT * FROM $this->table WHERE google_id = :google_id AND deleted_at is NULL";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['google_id' => $googleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registerUser($data)
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $status = 'active';
        $avatarUrl = null;

        $role = isset($data['role']) ? $data['role'] : 0;

        $sql = "INSERT INTO $this->table (name, phone, email, password, address, avatar_url, status, role, google_id) 
                VALUES (:name, :phone, :email, :password, :address, :avatar_url, :status, :role, :google_id)";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'address' => $data['address'],
            'avatar_url' => $avatarUrl,
            'status' => $status,
            'role' => $role,
            'google_id' => $data['google_id'] ?? ''
        ]);
    }

    public function isEmailExists($email)
    {
        $sql = "SELECT id FROM $this->table WHERE email = :email AND deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->rowCount() > 0;
    }

    public function isPhoneExists($phone)
    {
        $sql = "SELECT id FROM $this->table WHERE phone = :phone AND deleted_at is NULL";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['phone' => $phone]);
        return $stmt->rowCount() > 0;
    }

    public function findOrCreateUserFromGoogle($googleUser)
    {
        $user = $this->findUserByGoogleId($googleUser['id']);

        if ($user) {
            if (!empty($googleUser['picture']) && empty($user['avatar_url'])) {
                $this->updateUserAvatar($user['id'], $googleUser['picture']);
            }
            return $user;
        }

        $user = $this->findUserByEmail($googleUser['email']);

        if ($user) {
            if (!empty($googleUser['picture']) && empty($user['avatar_url'])) {
                $this->updateUserAvatar($user['id'], $googleUser['picture']);
            }
            $this->updateUserGoogleId($user['id'], $googleUser['id']);
            return $user;
        }

        $data = [
            'name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'password' => '',
            'phone' => '',
            'address' => '',
            'avatar_url' => $googleUser['picture'] ?? null,
            'status' => 'active',
            'role' => 0,
            'google_id' => $googleUser['id']
        ];

        $this->registerUser($data);
        return $this->findUserByEmail($googleUser['email']);
    }

    private function updateUserAvatar($userId, $avatarUrl)
    {
        $sql = "UPDATE $this->table SET avatar_url = :avatar_url WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['avatar_url' => $avatarUrl, 'id' => $userId]);
    }

    private function updateUserGoogleId($userId, $googleId)
    {
        $sql = "UPDATE $this->table SET google_id = :google_id WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['google_id' => $googleId, 'id' => $userId]);
    }
}
