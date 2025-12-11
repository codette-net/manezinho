<?php

namespace CMSOJ\Models;

use CMSOJ\Core\Model;
 
use PDO;

class Account extends Model
{
    protected string $table = 'accounts';

    public function findByEmail(string $email)
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
 