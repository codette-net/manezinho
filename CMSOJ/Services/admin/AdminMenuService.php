<?php

namespace CMSOJ\Services\Admin;

use CMSOJ\Models\MenuSection;

class AdminMenuService
{
  protected MenuSection $model;

  public function __construct()
  {
    $this->model = new MenuSection();
  }

  /**
   * Build parent → children tree
   */
  public function getTree(): array
  {
    $stmt = $this->model->db()->query(
      "SELECT * FROM menu_sections ORDER BY parent_id ASC, sort_order ASC"
    );

    $rows = $stmt->fetchAll();

    $tree = [];

    foreach ($rows as $row) {
      if (!$row['parent_id']) {
        $tree[$row['id']] = $row;
        $tree[$row['id']]['children'] = [];
      }
    }

    foreach ($rows as $row) {
      if ($row['parent_id'] && isset($tree[$row['parent_id']])) {
        $tree[$row['parent_id']]['children'][] = $row;
      }
    }

    return $tree;
  }

  public function deleteSection(int $id): void
  {
    $this->model->delete($id);
  }

  public function updateField(int $id, string $field, $value): void
  {
    // Whitelist allowed inline updates
    if (!in_array($field, ['sort_order', 'is_active'])) {
      return;
    }

    $this->model->update($id, [$field => $value]);
  }
  public function emptySection(): array
  {
    return [
      'id' => null,
      'parent_id' => null,
      'name_en' => '',
      'name_pt' => '',
      'description_en' => '',
      'description_pt' => '',
      'sort_order' => 0,
      'is_active' => 1
    ];
  }

  public function find(int $id): ?array
  {
    return $this->model->find($id) ?: null;
  }

  public function create(array $data): int
  {
    return $this->model->create($data);
  }

  public function update(int $id, array $data): void
  {
    $this->model->update($id, $data);
  }

  public function getParentOptions(?int $excludeId = null): array
  {
    $stmt = $this->model->db()->query(
      "SELECT id, name_en, name_pt
         FROM menu_sections
         WHERE parent_id IS NULL
         ORDER BY name_en ASC"
    );

    $parents = $stmt->fetchAll();

    if ($excludeId) {
      $parents = array_filter(
        $parents,
        fn($p) => $p['id'] !== $excludeId
      );
    }

    return $parents;
  }

  public function getParentSelectOptions(?int $excludeId = null): array
{
    $rows = $this->getParentOptions($excludeId); // your existing method

    $options = ['' => '— Top Level —'];

    foreach ($rows as $p) {
        $label = $p['name_en'];
        if (!empty($p['name_pt'])) {
            $label .= ' / ' . $p['name_pt'];
        }
        $options[$p['id']] = $label;
    }

    return $options;
}

}
