<?php 
namespace CMSOJ\Models;

use CMSOJ\Core\Database;

class Setting
{
    public static function get(string $key, $default = null)
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT value FROM settings WHERE `key` = ? LIMIT 1");
        $stmt->execute([$key]);
        $row = $stmt->fetchColumn();
        return $row !== false ? $row : $default;
    }

    public static function set(string $key, $value): void
    {
        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO settings (`key`, `value`) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE value = VALUES(value)
        ");
        $stmt->execute([$key, $value]);
    }

    public static function all(): array
    {
        $db = Database::connect();
        return $db->query("SELECT `key`, `value` FROM settings")->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
