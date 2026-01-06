<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;
use CMSOJ\Models\Page;
use CMSOJ\Services\Admin\PageStatsService;
use CMSOJ\Helpers\Flash;
use CMSOJ\Helpers\Csrf;
use CMSOJ\Helpers\Validator;
use CMSOJ\Helpers\Redirect;
use CMSOJ\Controllers\Admin\Concerns\Bulkable;

class PageController
{
    use Bulkable;

    public function index()
    {
        $model = new Page();
        $result = $model->listPages($_GET);

        $pages = $result['data'];
        $meta  = $result['meta'];

        $ids = array_map(fn($p) => (int)$p['id'], $pages);
        $counts = (new PageStatsService())->eventCounts($ids);

        $rows = [];
        foreach ($pages as $p) {
            $id = (int)$p['id'];

            $url = '--';
            if (!empty($p['url'])) {
                $safe = htmlspecialchars($p['url'], ENT_QUOTES, 'UTF-8');
                $url = "<a class='link1' href='{$safe}' target='_blank' rel='noopener'>{$safe}</a>";
            }

            $rows[] = [
                'id' => $id,
                'cells' => [
                    $id,
                    htmlspecialchars($p['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($p['slug'] ?? '', ENT_QUOTES, 'UTF-8'),
                    $url,
                    "<a class='link1' href='/admin/events?page_id={$id}'>" . number_format($counts[$id] ?? 0) . "</a>",
                    !empty($p['is_active']) ? 'Yes' : 'No',
                    "<a href='/admin/pages/edit/{$id}'>Edit</a>",
                ],
            ];
        }

        return Template::view('CMSOJ/Views/admin/pages/index.html', [
            'headers' => [
                'id' => 'ID',
                'title' => 'Title',
                'slug' => 'Slug',
                'url' => 'URL',
                'num_events' => 'Total Events',
                'is_active' => 'Active',
                'actions' => 'Actions',
            ],
            'rows' => $rows,
            'meta' => $meta,
            'query' => $_GET,
            'sortable' => $model->sortable,
            'bulk' => [
                'endpoint' => '/admin/pages/bulk',
                'actions' => $this->bulkActions(),
            ],
            'selected' => 'pages',
            'flash' => [
                'success' => Flash::get('success'),
                'error' => Flash::get('error'),
            ],
            'csrf' => Csrf::token(),
        ]);
    }

    public function create()
    {
        $now = date('Y-m-d H:i:s');

        return Template::view('CMSOJ/Views/admin/pages/form.html', [
            'page' => [
                'id' => null,
                'title' => '',
                'slug' => '',
                'description' => '',
                'url' => '',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            'editing' => false,
            'title' => 'Create Page',
            'selected' => 'pages',
            'csrf' => Csrf::token(),
            'flash' => [
                'success' => Flash::get('success'),
                'error' => Flash::get('error'),
            ],
        ]);
    }

    public function edit(int $id)
    {
        $model = new Page();
        $page = $model->find($id);

        if (!$page) {
            http_response_code(404);
            exit('Page not found');
        }

        return Template::view('CMSOJ/Views/admin/pages/form.html', [
            'page' => $page,
            'editing' => true,
            'title' => 'Edit Page',
            'selected' => 'pages',
            'csrf' => Csrf::token(),
            'flash' => [
                'success' => Flash::get('success'),
                'error' => Flash::get('error'),
            ],
        ]);
    }

    public function save()
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $validator = Validator::make($_POST, [
            'title' => 'required',
            // slug optional but recommended
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withOld()->send();
        }

        $id = (int)($_POST['id'] ?? 0);

        $title = trim((string)$_POST['title']);
        $slugInput = trim((string)($_POST['slug'] ?? ''));
        $slug = $slugInput !== '' ? $this->slugify($slugInput) : $this->slugify($title);

        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => trim((string)($_POST['description'] ?? '')),
            'url' => trim((string)($_POST['url'] ?? '')),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $model = new Page();

        if ($id > 0) {
            // keep created_at unchanged
            $model->update($id, $data);
            Flash::set('success', 'Page updated successfully.');
            header('Location: /admin/pages');
            exit;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $model->create($data);

        Flash::set('success', 'Page created successfully.');
        header('Location: /admin/pages');
        exit;
    }

    public function delete(int $id)
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        (new Page())->delete($id);

        Flash::set('success', 'Page deleted successfully.');
        header('Location: /admin/pages');
        exit;
    }

    protected function slugify(string $s): string
    {
        $s = strtolower(trim($s));
        $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? '';
        $s = trim($s, '-');
        return $s !== '' ? $s : 'page';
    }

    protected function bulkActions(): array
    {
        return [
            'delete' => [
                'label' => 'Delete',
                'permission' => 'pages.delete',
                'handler' => 'delete',
                'confirm' => 'Delete selected pages?',
            ],
            'activate' => [
                'label' => 'Activate',
                'permission' => 'pages.edit',
                'handler' => 'update',
                'data' => ['is_active' => 1],
            ],
            'deactivate' => [
                'label' => 'Deactivate',
                'permission' => 'pages.edit',
                'handler' => 'update',
                'data' => ['is_active' => 0],
            ],
        ];
    }

    public function bulk()
    {
        return $this->bulkEndpoint(new Page(), $this->bulkActions(), '/admin/pages', 'pages');
    }
}
