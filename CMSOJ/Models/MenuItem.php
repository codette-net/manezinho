<?php

namespace CMSOJ\Models;

use CMSOJ\Core\Database;
use PDO;

class MenuItem
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getActiveItems(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM menu_items
            WHERE is_active = 1
            ORDER BY section_id ASC, sort_order ASC, id ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
