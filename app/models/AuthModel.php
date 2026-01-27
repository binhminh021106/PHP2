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

    public function registerUser($data)
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $status = 'active';
        $avatarUrl = null;

        $sql = "INSERT INTO $this->table (name, phone, email, password, address, avatar_url, status) 
                VALUES (:name, :phone, :email, :password, :address, :avatar_url, :status)";
        
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
}