<?php

class AuthModel extends \Model
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

    public function findUserByFacebookId($facebookId)
    {
        $sql = "SELECT * FROM $this->table WHERE facebook_id = :facebook_id AND deleted_at is NULL";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['facebook_id' => $facebookId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registerUser($data)
    {
        $password = !empty($data['password']) ? $data['password'] : bin2hex(random_bytes(8));
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $status = $data['status'] ?? 'active';
        $avatarUrl = $data['avatar_url'] ?? null;
        $role = $data['role'] ?? 0;

        $phone = !empty($data['phone']) ? $data['phone'] : null;
        $address = !empty($data['address']) ? $data['address'] : null;

        $sql = "INSERT INTO $this->table (name, phone, email, password, address, avatar_url, status, role, google_id, facebook_id) 
                VALUES (:name, :phone, :email, :password, :address, :avatar_url, :status, :role, :google_id, :facebook_id)";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $phone,
            'email' => $data['email'],
            'password' => $hashedPassword,
            'address' => $address,
            'avatar_url' => $avatarUrl,
            'status' => $status,
            'role' => $role,
            'google_id' => $data['google_id'] ?? null,
            'facebook_id' => $data['facebook_id'] ?? null
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
        $googleId = $googleUser['id'] ?? $googleUser['google_id'] ?? null;
        $picture = $googleUser['picture'] ?? $googleUser['avatar_url'] ?? null;

        if ($googleId) {
            $user = $this->findUserByGoogleId($googleId);
            if ($user) {
                if (!empty($picture) && empty($user['avatar_url'])) {
                    $this->updateUserAvatar($user['id'], $picture);
                }
                return $user;
            }
        }

        $user = $this->findUserByEmail($googleUser['email']);

        if ($user) {
            if (!empty($picture) && empty($user['avatar_url'])) {
                $this->updateUserAvatar($user['id'], $picture);
            }
            if ($googleId) {
                $this->updateUserGoogleId($user['id'], $googleId);
            }
            return $user;
        }

        $data = [
            'name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'password' => '',
            'phone' => null,
            'address' => null,
            'avatar_url' => $picture,
            'status' => 'active',
            'role' => 0,
            'google_id' => $googleId
        ];

        $this->registerUser($data);
        return $this->findUserByEmail($googleUser['email']);
    }

    public function findOrCreateUserFromFacebook($facebookUser)
    {
        $facebookId = $facebookUser['id'] ?? $facebookUser['facebook_id'] ?? null;
        $picture = $facebookUser['picture'] ?? $facebookUser['avatar_url'] ?? null;

        if ($facebookId) {
            $user = $this->findUserByFacebookId($facebookId);
            if ($user) {
                if (!empty($picture) && empty($user['avatar_url'])) {
                    $this->updateUserAvatar($user['id'], $picture);
                }
                return $user;
            }
        }

        $user = $this->findUserByEmail($facebookUser['email']);

        if ($user) {
            if (!empty($picture) && empty($user['avatar_url'])) {
                $this->updateUserAvatar($user['id'], $picture);
            }
            if ($facebookId) {
                $this->updateUserFacebookId($user['id'], $facebookId);
            }
            return $user;
        }

        $data = [
            'name' => $facebookUser['name'],
            'email' => $facebookUser['email'],
            'password' => '',
            'phone' => null,
            'address' => null,
            'avatar_url' => $picture,
            'status' => 'active',
            'role' => 0,
            'facebook_id' => $facebookId
        ];

        $this->registerUser($data);
        return $this->findUserByEmail($facebookUser['email']);
    }

    public function saveResetToken($email, $token, $expiry)
    {
        $sql = "UPDATE $this->table SET reset_token = :token, reset_token_expire = :expiry WHERE email = :email";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'expiry' => $expiry,
            'email' => $email
        ]);
    }

    public function getUserByResetToken($token)
    {
        $sql = "SELECT * FROM $this->table WHERE reset_token = :token AND reset_token_expire > NOW() LIMIT 1";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE $this->table SET password = :password, reset_token = NULL, reset_token_expire = NULL WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $userId
        ]);
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

    private function updateUserFacebookId($userId, $facebookId)
    {
        $sql = "UPDATE $this->table SET facebook_id = :facebook_id WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['facebook_id' => $facebookId, 'id' => $userId]);
    }
}
