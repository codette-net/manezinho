<?php

namespace CMSOJ\Models;

use CMSOJ\Core\Database;
use PDO;

class MenuSection
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getActiveSections(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM menu_sections
            WHERE is_active = 1
            ORDER BY parent_id ASC, sort_order ASC, id ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
