<?php

namespace CMSOJ\Services\Admin;

use CMSOJ\Models\MenuItem;
use CMSOJ\Models\MenuSection;

class AdminMenuItemService
{
    protected MenuItem $items;
    protected MenuSection $sections;

    public function __construct()
    {
        $this->items    = new MenuItem();
        $this->sections = new MenuSection();
    }

    /**
     * List items with filters: section, search, status
     *
     * Returns a flat array (not paginated yet).
     */
    public function listItems(array $filters = []): array
    {
        $db = $this->items->db();

        $where  = [];
        $params = [];

        if (!empty($filters['section_id'])) {
            $where[]            = 'i.section_id = :sid';
            $params['sid']      = (int)$filters['section_id'];
        }

        if (!empty($filters['search'])) {
            $where[]            = '(i.name_en LIKE :search
                                OR i.description_en LIKE :search
                                OR i.description_pt LIKE :search)';
            $params['search']   = '%' . $filters['search'] . '%';
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $where[]            = 'i.is_active = :active';
            $params['active']   = $filters['status'] == '1' ? 1 : 0;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $db->prepare("
            SELECT i.*, s.name_en AS section_name
            FROM menu_items i
            LEFT JOIN menu_sections s ON s.id = i.section_id
            $whereSql
            ORDER BY s.id, i.sort_order ASC, i.id ASC
        ");

        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function emptyItem(): array
    {
        return [
            'id'             => null,
            'section_id'     => '',
            'display_type'   => 'item',
            'name_en'        => '',
            'name_pt'        => '',
            'description_en' => '',
            'description_pt' => '',
            'unit_1_label'   => '',
            'price_1'        => '',
            'unit_2_label'   => '',
            'price_2'        => '',
            'sort_order'     => 0,
            'is_active'      => 1,
        ];
    }

    public function find(int $id): ?array
    {
        return $this->items->find($id) ?: null;
    }

    public function create(array $data): int
    {
        return $this->items->create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->items->update($id, $data);
    }

    public function updateField(int $id, string $field, $value): void
    {
        $allowed = [
            'is_active',
            'sort_order',
            'name_en',
            'name_pt',
            'description_en',
            'description_pt',
            'unit_1_label',
            'unit_2_label',
            'price_1',
            'price_2',
            'display_type',
        ];

        if (!$id || !in_array($field, $allowed, true)) {
            return;
        }

        $this->items->update($id, [$field => $value]);
    }

    /**
     * For dropdowns & filters: active sections, id => label
     */
    public function getSectionOptions(): array
    {
        $stmt = $this->sections->db()->query("
            SELECT id, name_en
            FROM menu_sections
            WHERE is_active = 1
            ORDER BY parent_id, name_en
        ");

        $rows = $stmt->fetchAll();

        $options = ['' => 'All sections'];

        foreach ($rows as $row) {
            $options[$row['id']] = $row['name_en'];
        }

        return $options;
    }

    public function getSectionSelectOptionsForForm(): array
    {
        $stmt = $this->sections->db()->query("
            SELECT id, name_en
            FROM menu_sections
            WHERE is_active = 1
            ORDER BY parent_id, name_en
        ");

        $rows = $stmt->fetchAll();

        $options = ['' => 'Select a section...'];

        foreach ($rows as $row) {
            $options[$row['id']] = $row['name_en'];
        }

        return $options;
    }
}
