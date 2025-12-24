<?php

namespace CMSOJ\Models;
use CMSOJ\Core\Model;

use PDO;

class MenuItem extends Model
{
    
    protected string $table = 'menu_items';

    public function getActiveItems(): array
    {
        $stmt = $this->db()->query("
            SELECT * FROM {$this->table}
            WHERE is_active = 1
            ORDER BY section_id ASC, sort_order ASC, id ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
