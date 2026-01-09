<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;
use CMSOJ\Services\Admin\AdminMenuItemService;
use CMSOJ\Helpers\Validator;
use CMSOJ\Helpers\Redirect;
use CMSOJ\Helpers\Flash;
use CMSOJ\Helpers\Csrf;

class MenuItemController
{
    protected AdminMenuItemService $service;

    public function __construct()
    {
        $this->service = new AdminMenuItemService();
    }

    public function index()
    {
        $query   = $_GET;
        $items   = $this->service->listItems($query);
        $sectionsFilter = $this->service->getSectionOptions();
        // Transform into rows for the table component
        $rows = array_map(function ($i) {
            $id = (int)$i['id'];
            $returnTo = urlencode($_SERVER['REQUEST_URI']);

            $displayTypeSelect = '<select class="inline-edit" data-inline="display_type">
                <option value="item" '   . ($i['display_type'] === 'item'   ? 'selected' : '') . '>item</option>
                <option value="thead" '  . ($i['display_type'] === 'thead'  ? 'selected' : '') . '>thead</option>
                <option value="th" '     . ($i['display_type'] === 'th'     ? 'selected' : '') . '>th</option>
                <option value="divider" ' . ($i['display_type'] === 'divider' ? 'selected' : '') . '>divider</option>
            </select>';

            $cells = [
                $id,
                htmlspecialchars($i['section_name'] ?? '-', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($i['name_en'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($i['name_pt'] ?? '', ENT_QUOTES, 'UTF-8'),
                $displayTypeSelect,
                htmlspecialchars($i['description_en'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($i['description_pt'] ?? '', ENT_QUOTES, 'UTF-8'),
                '<input class="inline-edit" data-inline="unit_1_label" value="' . htmlspecialchars($i['unit_1_label'] ?? '', ENT_QUOTES, 'UTF-8') . '">',
                '<input class="inline-edit" data-inline="price_1" value="' . htmlspecialchars($i['price_1'] ?? '', ENT_QUOTES, 'UTF-8') . '">',
                '<input class="inline-edit" data-inline="unit_2_label" value="' . htmlspecialchars($i['unit_2_label'] ?? '', ENT_QUOTES, 'UTF-8') . '">',
                '<input class="inline-edit" data-inline="price_2" value="' . htmlspecialchars($i['price_2'] ?? '', ENT_QUOTES, 'UTF-8') . '">',
                '<input class="inline-edit" data-inline="sort_order" type="number" style="width:60px" value="' . (int)($i['sort_order'] ?? 0) . '">',
                '<input type="checkbox" class="toggle-active" data-inline="is_active" ' . (!empty($i['is_active']) ? 'checked' : '') . '>',
                "<a href=\"/admin/menu/items/edit/{$id}?return_to={$returnTo}\">Full Edit</a>",
            ];

            return [
                'id'    => $id,
                'cells' => $cells,
            ];
        }, $items);

        return Template::view('CMSOJ/Views/admin/menu/items.html', [
            'headers' => [
                'id'             => 'ID',
                'section_name'   => 'Section',
                'name_en'        => 'EN Name',
                'name_pt'        => 'PT Name',
                'display_type'   => 'Type',
                'description_en' => 'EN Desc',
                'description_pt' => 'PT Desc',
                'unit_1_label'   => 'Unit 1',
                'price_1'        => 'Price 1',
                'unit_2_label'   => 'Unit 2',
                'price_2'        => 'Price 2',
                'sort_order'     => 'Sort',
                'is_active'      => 'Active',
                'actions'        => '',
            ],
            'rows'     => $rows,
            'meta'     => null,
            'query'    => $query,
            'bulk'     => [
                'endpoint' => '/admin/menu/items/bulk', // not implemented yet
                'actions'  => [],
            ],
            'sortable'        => [],  // we handle sort via inline field
            'title'           => 'Menu Items',
            'selected'        => 'menu_items',
            'sectionsFilter'  => $sectionsFilter,
        ]);
    }

    public function create()
    {
        return Template::view('CMSOJ/Views/admin/menu/item-form.html', [
            'title'          => 'Create Menu Item',
            'item'           => $this->service->emptyItem(),
            'sectionOptions' => $this->service->getSectionSelectOptionsForForm(),
            'editing'        => false,
        ]);
    }

    public function edit(int $id)
    {
        $item = $this->service->find($id);

        if (!$item) {
            http_response_code(404);
            exit('Item not found');
        }

        return Template::view('CMSOJ/Views/admin/menu/item-form.html', [
            'title'          => 'Edit Menu Item',
            'item'           => $item,
            'sectionOptions' => $this->service->getSectionSelectOptionsForForm(),
            'editing'        => true,
        ]);
    }

    public function store()
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $validator = Validator::make($_POST, [
            'section_id'   => 'required',
            'name_en'      => 'required',
            'display_type' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withOld()
                ->send();
        }

        $data = $this->extractItemData($_POST);

        $this->service->create($data);

        Flash::set('success', 'Menu item created successfully.');
        $returnTo = $_POST['return_to'] ?? '';
        return Redirect::toReturnTo($returnTo, '/admin/menu/items')->send();
    }

    public function update(int $id)
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $validator = Validator::make($_POST, [
            'section_id'   => 'required',
            'name_en'      => 'required',
            'display_type' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withOld()
                ->send();
        }

        $data = $this->extractItemData($_POST);

        $this->service->update($id, $data);

        Flash::set('success', 'Menu item updated successfully.');
        $returnTo = $_POST['return_to'] ?? '';
        return Redirect::toReturnTo($returnTo, '/admin/menu/items')->send();
    }

    protected function extractItemData(array $input): array
    {
        return [
            'section_id'     => $input['section_id'] ?: null,
            'display_type'   => $input['display_type'] ?? 'item',
            'name_en'        => trim($input['name_en'] ?? ''),
            'name_pt'        => trim($input['name_pt'] ?? ''),
            'description_en' => trim($input['description_en'] ?? ''),
            'description_pt' => trim($input['description_pt'] ?? ''),
            'unit_1_label'   => trim($input['unit_1_label'] ?? ''),
            'price_1'        => trim($input['price_1'] ?? ''),
            'unit_2_label'   => trim($input['unit_2_label'] ?? ''),
            'price_2'        => trim($input['price_2'] ?? ''),
            'sort_order'     => (int)($input['sort_order'] ?? 0),
            'is_active'      => isset($input['is_active']) ? 1 : 0,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
    }

    public function updateInline()
    {
        $id    = (int)($_POST['id'] ?? 0);
        $field = $_POST['field'] ?? '';
        $value = $_POST['value'] ?? '';

        if ($id && $field) {
            $this->service->updateField($id, $field, $value);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}
