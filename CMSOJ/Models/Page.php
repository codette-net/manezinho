<?php

namespace CMSOJ\Models;

use CMSOJ\Core\Model;

class Page extends Model
{
    protected string $table = 'pages';

    public array $sortable = [
        'id', 'title', 'url', 'is_active', 'updated_at', 'created_at'
    ];

    public array $searchable = [
        'title', 'description', 'url', 'slug'
    ];

    public array $bulkUpdatable = [
        'is_active'
    ];

    public function listPages(array $params = []): array
    {
        return $this->list([
            'columns'  => $this->sortable,
            'searchIn' => $this->searchable,
            'search'   => trim($params['q'] ?? $params['search_query'] ?? ''),
            'sort'     => $params['sort'] ?? 'id',
            'dir'      => $params['dir'] ?? 'asc',
            'page'     => (int)($params['page'] ?? 1),
            'perPage'  => (int)($params['perPage'] ?? 20),
        ]);
    }
}
