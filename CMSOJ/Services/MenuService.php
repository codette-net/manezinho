<?php

namespace CMSOJ\Services;

use CMSOJ\Models\MenuSection;
use CMSOJ\Models\MenuItem;

class MenuService
{
    private MenuSection $sectionModel;
    private MenuItem $itemModel;

    public function __construct()
    {
        $this->sectionModel = new MenuSection();
        $this->itemModel    = new MenuItem();
    }

    public function getMenu(string $lang = 'en'): array
    {
        $sections = $this->sectionModel->getActiveSections();
        $items    = $this->itemModel->getActiveItems();

        // Build tree for sections
        $tree = [];
        foreach ($sections as $s) {
            if (empty($s['parent_id'])) {
                $s['children'] = [];
                $tree[$s['id']] = $s;
            }
        }

        foreach ($sections as $s) {
            if (!empty($s['parent_id']) && isset($tree[$s['parent_id']])) {
                $tree[$s['parent_id']]['children'][] = $s;
            }
        }

        // Group items by section
        $itemsBySection = [];
        foreach ($items as $i) {
            $itemsBySection[$i['section_id']][] = $i;
        }

        return [
            'title'           => 'Menu',
            'lang'            => $lang,
            'tree'            => $tree,
            'itemsBySection'  => $itemsBySection,
        ];
    }
}
