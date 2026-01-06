<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;
use CMSOJ\Services\Admin\AdminEventService;
use CMSOJ\Helpers\Flash;
use CMSOJ\Helpers\Permissions;
use CMSOJ\Helpers\Validator;
use CMSOJ\Helpers\Redirect;
use CMSOJ\Helpers\Csrf;
use CMSOJ\Controllers\Admin\Concerns\Bulkable;
use CMSOJ\Models\Event;


class EventController
{
  protected AdminEventService $service;
  use Bulkable;

  public function __construct()
  {
    $this->service = new AdminEventService();
  }

  public function index()
  {
    $result = $this->service->search($_GET);

    $rows = [];
    foreach ($result['items'] as $e) {
      $id = (int)$e['id'];

      $rows[] = [
        'id' => $id,
        'cells' => [
          $id,
          $this->titleCell($e),
          htmlspecialchars((string)($e['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
          $this->fmtDate($e['datestart'] ?? ''),
          $this->fmtDate($e['dateend'] ?? ''),
          ($e['recurring'] ?? 'never') !== 'never' ? '<span class="tag">' . htmlspecialchars(ucfirst($e['recurring']), ENT_QUOTES, 'UTF-8') . '</span>' : '--',
          $this->statusBadge($e),
          $this->pageIdCell($e),
          $this->actionLinks($id),
        ],
      ];
    }

    return Template::view('CMSOJ/Views/admin/events/index.html', [
      'headers' => [
        'id' => 'ID',
        'title' => 'Title',
        'description' => 'Description',
        'datestart' => 'Start',
        'dateend' => 'End',
        'recurring' => 'Recurring',
        'status' => 'Status',
        'uid' => 'Page ID',
        'actions' => 'Actions',
      ],
      'rows' => $rows,
      'meta' => $result['meta'],
      'query' => $_GET,
      'sortable' => ['id', 'title', 'description', 'datestart', 'dateend', 'recurring', 'uid'],
      'title' => 'Events',
      'selected' => 'events',
      'flash' => [
        'success' => Flash::get('success'),
        'error' => Flash::get('error'),
      ],
      'csrf' => Csrf::token(),
      'bulk' => [
        'endpoint' => '/admin/events/bulk',
        'actions'  => $this->bulkActions(),
      ],
    ]);
  }

  public function create()
  {
    return Template::view('CMSOJ/Views/admin/events/form.html', [
      'event' => $this->service->emptyEvent(),
      'editing' => false,
      'title' => 'Create Event',
      'csrf' => Csrf::token(),
    ]);
  }

  public function edit(int $id)
  {
    $event = $this->service->find($id);
    if (!$event) {
      http_response_code(404);
      exit('Event not found');
    }

    // normalize for datetime-local inputs
    $event['datestart'] = $this->toDatetimeLocal($event['datestart'] ?? '');
    $event['dateend'] = $this->toDatetimeLocal($event['dateend'] ?? '');
    $event['submit_date'] = $this->toDatetimeLocal($event['submit_date'] ?? '');

    return Template::view('CMSOJ/Views/admin/events/form.html', [
      'event' => $event,
      'editing' => true,
      'title' => 'Edit Event',
      'csrf' => Csrf::token(),
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
      'datestart' => 'required',
      'dateend' => 'required',
      'recurring' => 'required',
      'submit_date' => 'required',
    ]);

    if ($validator->fails()) {
      return Redirect::back()
        ->withErrors($validator)
        ->withOld()
        ->send();
    }

    // basic sanity check: end >= start
    $start = strtotime((string)($_POST['datestart'] ?? ''));
    $end   = strtotime((string)($_POST['dateend'] ?? ''));
    if ($start && $end && $end < $start) {
      Flash::set('error', 'End date must be after start date.');
      return Redirect::back()->withOld()->send();
    }

    $photoUrl = $this->handleUpload('photo');

    $data = [
      'uid' => ($_POST['uid'] ?? '') !== '' ? (int)$_POST['uid'] : 1,
      'title' => trim((string)$_POST['title']),
      'description' => trim((string)($_POST['description'] ?? '')),
      'color' => ($_POST['color'] ?? '') !== '' ? trim((string)$_POST['color']) : '#2163BA',
      'datestart' => $this->normalizeDateTime((string)$_POST['datestart']),
      'dateend' => $this->normalizeDateTime((string)$_POST['dateend']),
      'recurring' => (string)$_POST['recurring'],
      'redirect_url' => trim((string)($_POST['redirect_url'] ?? '')),
      'submit_date' => $this->normalizeDateTime((string)$_POST['submit_date']),
    ];

    // only set photo_url if a new file uploaded
    if ($photoUrl) {
      $data['photo_url'] = $photoUrl;
    }

    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
      $this->service->update($id, $data);
      Flash::set('success', 'Event updated successfully.');
      header('Location: /admin/events');
      exit;
    }

    $this->service->create($data);
    Flash::set('success', 'Event created successfully.');
    header('Location: /admin/events');
    exit;
  }

  public function delete(int $id)
  {
    if (!Csrf::validate($_POST['_csrf'] ?? null)) {
      http_response_code(403);
      exit('Invalid CSRF token.');
    }

    $this->service->delete($id);
    Flash::set('success', 'Event deleted successfully.');
    header('Location: /admin/events');
    exit;
  }

  // ---------------- helpers ----------------

  protected function handleUpload(string $field): ?string
  {
    if (!isset($_FILES[$field]) || empty($_FILES[$field]['tmp_name'])) {
      return null;
    }

    $tmp = $_FILES[$field]['tmp_name'];
    if (@getimagesize($tmp) === false) {
      Flash::set('error', 'Uploaded file must be an image.');
      return null;
    }

    $ext = strtolower(pathinfo($_FILES[$field]['name'] ?? '', PATHINFO_EXTENSION));
    $ext = preg_replace('/[^a-z0-9]/', '', $ext);
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
      Flash::set('error', 'Unsupported image type.');
      return null;
    }

    // keep consistent with your legacy: store under /uploads
    $name = md5(uniqid((string)mt_rand(), true)) . '.' . $ext;
    $relative = 'uploads/' . $name;

    $target = dirname(__DIR__, 3) . '/public/' . $relative;
    if (!is_dir(dirname($target))) {
      @mkdir(dirname($target), 0775, true);
    }

    if (!move_uploaded_file($tmp, $target)) {
      Flash::set('error', 'Failed to upload image.');
      return null;
    }

    return $relative;
  }

  protected function normalizeDateTime(string $dt): string
  {
    // from datetime-local
    if (str_contains($dt, 'T')) {
      return str_replace('T', ' ', $dt) . ':00';
    }
    return $dt;
  }

  protected function toDatetimeLocal(string $dt): string
  {
    if (!$dt) return date('Y-m-d\TH:i');
    $ts = strtotime($dt);
    return $ts ? date('Y-m-d\TH:i', $ts) : date('Y-m-d\TH:i');
  }

  protected function fmtDate(string $dt): string
  {
    $ts = strtotime($dt);
    return $ts ? date('Y-m-d H:i', $ts) : '';
  }

  protected function statusBadge(array $e): string
  {
    $start = strtotime((string)($e['datestart'] ?? ''));
    $end = strtotime((string)($e['dateend'] ?? ''));
    $rec = (string)($e['recurring'] ?? 'never');
    $now = time();

    if ($rec !== 'never') return '<span class="badge green">Active</span>';
    if ($start && $end && $start <= $now && $end >= $now) return '<span class="badge green">Active</span>';
    if ($end && $end < $now) return '<span class="badge red">Ended</span>';
    if ($start && $start > $now) return '<span class="badge grey">Upcoming</span>';
    return '<span class="badge grey">—</span>';
  }

  protected function titleCell(array $e): string
  {
    $color = htmlspecialchars((string)($e['color'] ?? '#2163BA'), ENT_QUOTES, 'UTF-8');
    $title = htmlspecialchars((string)($e['title'] ?? ''), ENT_QUOTES, 'UTF-8');

    return "<span class=\"event-color\" style=\"color:{$color}\" title=\"{$color}\">■</span> <strong>{$title}</strong>";
  }

  protected function pageIdCell(array $e): string
  {
    $uid = (int)($e['uid'] ?? 0);
    $url = (string)($e['url'] ?? '');

    if ($url !== '') {
      $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
      return "<a class=\"link\" href=\"{$safeUrl}\" target=\"_blank\" rel=\"noopener\">{$uid}</a>";
    }

    return (string)$uid;
  }

  protected function actionLinks(int $id): string
  {
    $csrf = Csrf::token();

    return
      "<a href=\"/admin/events/edit/{$id}\">Edit</a> " .
      "<form method=\"POST\" action=\"/admin/events/delete/{$id}\" style=\"display:inline\" onsubmit=\"return confirm('Delete this event?');\">
        <input type=\"hidden\" name=\"_csrf\" value=\"{$csrf}\">
        <button type=\"submit\" class=\"link-button danger\">Delete</button>
      </form>";
  }

  protected function bulkActions(): array
  {
    return [
      'delete' => [
        'label'      => 'Delete',
        'permission' => 'events.delete',
        'handler'    => 'delete',
        'confirm'    => 'Delete selected events?',
      ],
      'recurring_never' => [
        'label'      => 'Recurring: Never',
        'permission' => 'events.edit',
        'handler'    => 'update',
        'data'       => ['recurring' => 'never'],
      ],
      'recurring_daily' => [
        'label'      => 'Recurring: Daily',
        'permission' => 'events.edit',
        'handler'    => 'update',
        'data'       => ['recurring' => 'daily'],
      ],
      'recurring_weekly' => [
        'label'      => 'Recurring: Weekly',
        'permission' => 'events.edit',
        'handler'    => 'update',
        'data'       => ['recurring' => 'weekly'],
      ],
      'recurring_monthly' => [
        'label'      => 'Recurring: Monthly',
        'permission' => 'events.edit',
        'handler'    => 'update',
        'data'       => ['recurring' => 'monthly'],
      ],
      'recurring_yearly' => [
        'label'      => 'Recurring: Yearly',
        'permission' => 'events.edit',
        'handler'    => 'update',
        'data'       => ['recurring' => 'yearly'],
      ],
    ];
  }

  public function bulk()
  {
    // Uses the shared trait method 
    return $this->bulkEndpoint(
      new Event(),
      $this->bulkActions(),
      '/admin/events',
      'events'
    );
  }
}
