<?php

namespace CMSOJ\Models;

use CMSOJ\Core\Model;
use PDO;

class MenuSection extends Model
{
    protected string $table = 'menu_sections';

    public function getActiveSections(): array
    {
        $stmt = $this->db()->query("
            SELECT *
            FROM {$this->table}
            WHERE is_active = 1
            ORDER BY parent_id ASC, sort_order ASC, id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
