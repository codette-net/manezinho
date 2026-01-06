<?php

namespace CMSOJ\Models;

use CMSOJ\Core\Model;
use PDO;

class PageDetail extends Model
{
    protected string $table = 'event_page_details';
    protected string $primaryKey = 'page_id';

    public array $sortable = ['page_id', 'title', 'description', 'url'];
    public array $searchable = ['title', 'description', 'url'];

    public function findByPageId(int $pageId): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE page_id = ? LIMIT 1");
        $stmt->execute([$pageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Upsert helper using core Model CRUD.
     * Keeps controllers clean.
     */
    public function upsert(int $pageId, array $data): void
    {
        $existing = $this->findByPageId($pageId);

        $payload = [
            'title' => trim((string)($data['title'] ?? '')),
            'description' => trim((string)($data['description'] ?? '')),
            'url' => trim((string)($data['url'] ?? '')),
        ];

        if ($existing) {
            $this->update($pageId, $payload);
        } else {
            $payload['page_id'] = $pageId;
            $this->create($payload);
        }
    }
}
