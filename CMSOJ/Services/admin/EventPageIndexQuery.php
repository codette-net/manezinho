<?php

namespace CMSOJ\Services\Admin;

use CMSOJ\Core\Database;
use PDO;

class EventPageIndexQuery
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function list(array $q): array
    {
        $page    = max(1, (int)($q['page'] ?? 1));
        $perPage = min(100, max(1, (int)($q['perPage'] ?? 20)));
        $offset  = ($page - 1) * $perPage;

        $search = trim((string)($q['search_query'] ?? ''));
        $params = [];

        // Sorting compatible with your table component (?sort=...&dir=...)
        $sortable = ['uid', 'title', 'description', 'url', 'num_events'];
        $sortKey  = (string)($q['sort'] ?? 'uid');
        $sort     = in_array($sortKey, $sortable, true) ? $sortKey : 'uid';
        $dir      = strtolower((string)($q['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';

        $where = '';
        if ($search !== '') {
            $where = "WHERE (
                CAST(e.uid AS CHAR) LIKE :s
                OR epd.title LIKE :s
                OR epd.description LIKE :s
                OR epd.url LIKE :s
            )";
            $params['s'] = '%' . $search . '%';
        }

        // Total distinct pages (uids present in events)
        $sqlTotal = "
            SELECT COUNT(*) FROM (
                SELECT e.uid
                FROM events e
                LEFT JOIN event_page_details epd ON epd.page_id = e.uid
                {$where}
                GROUP BY e.uid
            ) x
        ";
        $stmt = $this->db->prepare($sqlTotal);
        foreach ($params as $k => $v) $stmt->bindValue(':' . $k, $v);
        $stmt->execute();
        $total = (int)$stmt->fetchColumn();

        // Main list: group by uid, include details + count
        $sql = "
            SELECT
                e.uid,
                epd.title,
                epd.description,
                epd.url,
                COUNT(e.id) AS num_events
            FROM events e
            LEFT JOIN event_page_details epd ON epd.page_id = e.uid
            {$where}
            GROUP BY e.uid, epd.title, epd.description, epd.url
            ORDER BY {$sort} {$dir}
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue(':' . $k, $v);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'meta' => [
                'page' => $page,
                'pages' => max(1, (int)ceil($total / $perPage)),
                'perPage' => $perPage,
                'total' => $total,
            ],
        ];
    }
}
