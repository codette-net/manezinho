<?php

namespace CMSOJ\Services\Admin;

use CMSOJ\Core\Database;
use PDO;

class AdminEventService
{
  protected PDO $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function emptyEvent(): array
  {
    $now = date('Y-m-d H:i:s');

    return [
      'id' => null,
      'title' => '',
      'description' => '',
      'color' => '#2163BA',
      'datestart' => date('Y-m-d\TH:i'),
      'dateend' => date('Y-m-d\TH:i'),
      'uid' => 1,
      'submit_date' => date('Y-m-d\TH:i'),
      'recurring' => 'never',
      'photo_url' => '',
      'redirect_url' => '',
    ];
  }

  public function find(int $id): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

  /**
   * Legacy-compatible listing:
   * - search in title/description
   * - filters: recurring, datestart, dateend, status, page_id
   * - sorting whitelist
   * - pagination
   */
  public function search(array $q): array
  {
    $page     = max(1, (int)($q['page'] ?? 1));
    $perPage  = min(100, max(1, (int)($q['per_page'] ?? 20)));
    $offset   = ($page - 1) * $perPage;

    $search   = trim((string)($q['search_query'] ?? ''));
    $recurring = (string)($q['recurring'] ?? '');
    $datestart = (string)($q['datestart'] ?? '');
    $dateend   = (string)($q['dateend'] ?? '');
    $status    = (string)($q['status'] ?? '');
    $pageId    = (string)($q['page_id'] ?? '');

    $order = (isset($q['order']) && strtoupper((string)$q['order']) === 'DESC') ? 'DESC' : 'ASC';
    $whitelist = ['id','title','description','color','datestart','dateend','recurring','photo_url','submit_date','uid'];
    // $orderBy = in_array(($q['order_by'] ?? 'id'), $whitelist, true) ? $q['order_by'] : 'id';

    $where = [];
    $params = [];

    if ($search !== '') {
      $where[] = "(e.title LIKE :search OR e.description LIKE :search)";
      $params['search'] = '%' . $search . '%';
    }
    if ($recurring !== '') {
      $where[] = "e.recurring = :recurring";
      $params['recurring'] = $recurring;
    }
    if ($datestart !== '') {
      // accept datetime-local format; DB expects "Y-m-d H:i:s" or compatible
      $where[] = "e.datestart >= :datestart";
      $params['datestart'] = $this->normalizeDateTime($datestart);
    }
    if ($dateend !== '') {
      $where[] = "e.dateend <= :dateend";
      $params['dateend'] = $this->normalizeDateTime($dateend);
    }

    $now = date('Y-m-d H:i:s');
    if ($status === 'active') {
      $where[] = "e.datestart <= :now AND e.dateend >= :now";
      $params['now'] = $now;
    } elseif ($status === 'upcoming') {
      $where[] = "e.datestart >= :now";
      $params['now'] = $now;
    } elseif ($status === 'ended') {
      $where[] = "e.dateend < :now";
      $params['now'] = $now;
    }

    if ($pageId !== '') {
      $where[] = "e.uid = :page_id";
      $params['page_id'] = (int)$pageId;
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    // total
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM events e {$whereSql}");
    foreach ($params as $k => $v) $stmt->bindValue(':' . $k, $v);
    $stmt->execute();
    $total = (int)$stmt->fetchColumn();

    // rows (+ optional join to event_page_details like legacy)
    $sql = "
      SELECT e.*, epd.url
      FROM events e
      LEFT JOIN event_page_details epd ON epd.page_id = e.uid
      {$whereSql}
      ORDER BY id {$order}
      LIMIT :offset, :perpage
    ";
    $stmt = $this->db->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue(':' . $k, $v);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perpage', $perPage, PDO::PARAM_INT);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
      'items' => $items,
      'meta' => [
        'page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'pages' => max(1, (int)ceil($total / $perPage)),
        'order' => $order,
        // 'order_by' => $orderBy,
      ],
    ];
  }

  public function create(array $data): int
  {
    $cols = array_keys($data);
    $place = implode(',', array_fill(0, count($cols), '?'));
    $sql = "INSERT INTO events (" . implode(',', $cols) . ") VALUES ({$place})";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(array_values($data));
    return (int)$this->db->lastInsertId();
  }

  public function update(int $id, array $data): void
  {
    $set = implode(', ', array_map(fn($c) => "{$c} = ?", array_keys($data)));
    $sql = "UPDATE events SET {$set} WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([...array_values($data), $id]);
  }

  public function delete(int $id): void
  {
    $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
  }

  private function normalizeDateTime(string $dt): string
  {
    // supports "2026-01-05T12:30" and already-normal strings
    if (str_contains($dt, 'T')) {
      return str_replace('T', ' ', $dt) . ':00';
    }
    return $dt;
  }
}
