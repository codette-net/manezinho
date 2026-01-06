<?php

namespace CMSOJ\Services\Admin;

use CMSOJ\Core\Database;
use PDO;

class PageStatsService
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Returns [pageId => numEvents]
     */
    public function eventCounts(array $pageIds): array
    {
        $pageIds = array_values(array_unique(array_filter(array_map('intval', $pageIds))));
        if (!$pageIds) return [];

        $in = implode(',', array_fill(0, count($pageIds), '?'));

        // If you migrate events.page_id use that.
        // If you keep uid, replace page_id with uid.
        $stmt = $this->db->prepare("
            SELECT page_id, COUNT(*) AS cnt
            FROM events
            WHERE page_id IN ({$in})
            GROUP BY page_id
        ");
        $stmt->execute($pageIds);

        $map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $map[(int)$row['page_id']] = (int)$row['cnt'];
        }
        return $map;
    }
}
