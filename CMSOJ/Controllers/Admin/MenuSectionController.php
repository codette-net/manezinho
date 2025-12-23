<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;
use CMSOJ\Services\Admin\AdminMenuService;
use CMSOJ\Helpers\Flash;
use CMSOJ\Helpers\Validator;
use CMSOJ\Helpers\Redirect;
use CMSOJ\Helpers\Csrf;

class MenuSectionController
{
  protected AdminMenuService $service;

  public function __construct()
  {
    $this->service = new AdminMenuService();
  }

  public function index()
  {
    $tree = $this->service->getTree();

    $rows = [];

    foreach ($tree as $main) {
      $id = (int)$main['id'];

      $rows[] = [
        'id' => $id,
        'cells' => [
          $id,
          '<strong>' . htmlspecialchars($main['name_en'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong>',
          htmlspecialchars($main['name_pt'] ?? '', ENT_QUOTES, 'UTF-8'),
          '-', // parent
          // sort input
          '<input type="number"
                             class="inline-edit"
                             data-inline="sort_order"
                             value="' . (int)($main['sort_order'] ?? 0) . '">',
          // active toggle
          '<input type="checkbox"
                             class="toggle-active"
                             data-inline="is_active"
                             ' . (!empty($main['is_active']) ? 'checked' : '') . '>',
          // actions
          $this->actionLinks($id, false),
        ],
      ];

      if (!empty($main['children'])) {
        foreach ($main['children'] as $sub) {
          $sid = (int)$sub['id'];

          $rows[] = [
            'id' => $sid,
            'cells' => [
              $sid,
              '&nbsp;&nbsp;&nbsp;↳ ' . htmlspecialchars($sub['name_en'] ?? '', ENT_QUOTES, 'UTF-8'),
              htmlspecialchars($sub['name_pt'] ?? '', ENT_QUOTES, 'UTF-8'),
              htmlspecialchars($main['name_en'] ?? '', ENT_QUOTES, 'UTF-8'),
              '<input type="number"
                                     class="inline-edit"
                                     data-inline="sort_order"
                                     value="' . (int)($sub['sort_order'] ?? 0) . '">',
              '<input type="checkbox"
                                     class="toggle-active"
                                     data-inline="is_active"
                                     ' . (!empty($sub['is_active']) ? 'checked' : '') . '>',
              $this->actionLinks($sid, true),
            ],
          ];
        }
      }
    }

    return Template::view('CMSOJ/Views/admin/menu/sections.html', [
      'headers' => [
        'id'         => 'ID',
        'name_en'    => 'Name (EN)',
        'name_pt'    => 'Name (PT)',
        'parent'     => 'Parent',
        'sort_order' => 'Sort',
        'is_active'  => 'Active',
        'actions'    => 'Actions',
      ],
      'rows'     => $rows,
      'meta'     => null,          // no pagination for sections
      'query'    => $_GET,         // for consistency with table component
      'bulk'     => [              // no bulk actions yet
        'endpoint' => '/admin/menu/sections/bulk',
        'actions'  => [],
      ],
      'sortable' => [],             // we manage sort via inline inputs
      'title'    => 'Menu Sections',
      'selected' => 'menu_sections',
    ]);
  }

  protected function actionLinks(int $id, bool $isSub): string
  {
    $label = $isSub ? 'Delete subsection?' : 'Delete section?';

    // CSRF for delete
    $csrf = \CMSOJ\Helpers\Csrf::token();

    return
      "<a href='/admin/menu/items?section_id={$id}'>Items</a> " .
      "<a href='/admin/menu/sections/edit/{$id}'>Edit</a> " .
      "<form method='POST'
                    action='/admin/menu/sections/delete/{$id}'
                    style='display:inline'
                    onsubmit='return confirm(\"{$label}\");'>
               <input type='hidden' name='_csrf' value='{$csrf}'>
               <button type='submit' class='link-button'>Delete</button>
             </form>";
  }

  public function create()
  {
    return Template::view('CMSOJ/Views/admin/menu/section-form.html', [
      'section'       => $this->service->emptySection(),
      'parentOptions' => $this->service->getParentSelectOptions(),
      'editing'       => false,
      'title'         => 'Create Menu Section',
    ]);
  }

  public function edit(int $id)
  {
    $section = $this->service->find($id);

    if (!$section) {
      http_response_code(404);
      exit('Section not found');
    }

    return Template::view('CMSOJ/Views/admin/menu/section-form.html', [
      'section'       => $section,
      'parentOptions' => $this->service->getParentSelectOptions($id) ,
      'editing'       => true,
      'title'         => 'Edit Menu Section',
    ]);
  }

  public function save()
  {
    if (!Csrf::validate($_POST['_csrf'] ?? null)) {
      http_response_code(403);
      exit('Invalid CSRF token.');
    }

    $validator = Validator::make($_POST, [
      'name_en' => 'required',
    ]);

    if ($validator->fails()) {
      return Redirect::back()
        ->withErrors($validator)
        ->withOld()
        ->send();
    }

    $data = [
      'parent_id'      => $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null,
      'name_en'        => trim($_POST['name_en']),
      'name_pt'        => trim($_POST['name_pt'] ?? ''),
      'description_en' => trim($_POST['description_en'] ?? ''),
      'description_pt' => trim($_POST['description_pt'] ?? ''),
      'sort_order'     => (int)($_POST['sort_order'] ?? 0),
      'is_active'      => isset($_POST['is_active']) ? 1 : 0,
      'updated_at'     => date('Y-m-d H:i:s'),
    ];

    if (!empty($_POST['id'])) {
      $id = (int)$_POST['id'];
      $this->service->update($id, $data);
      Flash::set('success', 'Section updated successfully.');
    } else {
      $this->service->create($data);
      Flash::set('success', 'Section created successfully.');
    }

    header('Location: /admin/menu/sections');
    exit;
  }

  public function delete(int $id)
  {
    if (!Csrf::validate($_POST['_csrf'] ?? null)) {
      http_response_code(403);
      exit('Invalid CSRF token.');
    }

    $this->service->deleteSection($id);

    Flash::set('success', 'Section deleted successfully.');
    header('Location: /admin/menu/sections');
    exit;
  }

  public function updateInline()
  {
    $id    = (int)($_POST['id'] ?? 0);
    $field = $_POST['field'] ?? null;
    $value = $_POST['value'] ?? null;

    if ($id && $field !== null) {
      $this->service->updateField($id, $field, $value);
    }

    echo json_encode(['status' => 'ok']);
    exit;
  }
}
