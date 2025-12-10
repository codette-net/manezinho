<?php

namespace CMSOJ\Models;

use CMSOJ\Core\Database;
use PDO;

class Account
{
    public static function all(): array
    {
        $db = Database::connect();
        return $db->query("SELECT * FROM accounts ORDER BY id ASC")
                  ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(string $email): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM accounts WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public static function create(array $data): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO accounts (name, display_name, email, password)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['name'],
            $data['display_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::connect();

        $sql = "
            UPDATE accounts
            SET name = ?, display_name = ?, email = ?
        ";

        $params = [
            $data['name'],
            $data['display_name'],
            $data['email']
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ? LIMIT 1";
        $params[] = $id;

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
}
