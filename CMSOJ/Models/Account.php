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

    public function listAccounts(array $params = [])
    {
        return $this->list([
            'columns'  => ['id', 'email', 'role', 'updated_at', 'last_seen', 'display_name', 'name'],
            'sort'     => $params['sort'] ?? 'id',
            'dir'      => $params['dir'] ?? 'desc',
            'page'     => $params['page'] ?? 1,
            'perPage'  => $params['perPage'] ?? 5,
            'search'   => $params['search'] ?? null,
            'searchIn' => ['email', 'role', 'display_name', 'name']
        ]);
    }
}
