<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> Events  | CMSOJ </title>
  
<link rel="stylesheet" href='<?= \CMSOJ\Template::asset("/assets/css/classless.css") ?>' />
<link rel="stylesheet" href='<?= \CMSOJ\Template::asset("/assets/css/admin_new.css") ?>' />

<noscript>
  <link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/noscript.css"); ?>' />
</noscript>

</head>

<body class="<?php echo $body_class ?? 'admin-main'; ?>">
  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/flash.html', []); ?>

  
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="logo">CMSOJ Admin</div>

<nav class="menu">
    <ul class="sidebar-nav">
        <li><a href="/admin" class="<?= $selected === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="/admin/pages" class="<?= $selected === 'pages' ? 'active' : '' ?>">Pages</a></li>
        <li><a href="/admin/events" class="<?= $selected === 'events' ? 'active' : '' ?>">Events</a></li>
        <!-- todo : make submenu's  -->
        <li><a href="/admin/menu/sections" class="<?= $selected === 'menu_sections' ? 'active' : '' ?>">Menu Sections</a></li>
        <li><a href="/admin/menu/items" class="<?= $selected === 'menu_items' ? 'active' : '' ?>">Menu Items</a></li>


        <li><a href="/admin/messages" class="<?= $selected === 'messages' ? 'active' : '' ?>">Messages</a></li>
        <?php if (strtolower($_SESSION['admin_role']) === 'admin') : ?>
        <li><a href="/admin/accounts" class="<?= $selected === 'accounts' ? 'active' : '' ?>">Accounts</a></li>
        <?php endif; ?>
        
        <li><a href="/admin/profile" class="<?= $selected === 'Profile' ? 'active' : '' ?>">My Profile</a></li>
        <li><a href="/admin/settings" class="<?= $selected === 'settings' ? 'active' : '' ?>">Settings</a></li>
    </ul>
</nav>

<div class="logout">
    <a href="/admin/logout">Logout</a>
</div>  
    </aside>


  

    <main class="admin-content">
        <header class="admin-header">

    <div class="breadcrumbs">
        <strong><?= $title ?? '' ?></strong>
    </div>

    <div class="profile">
        <span class="name"><?= $_SESSION['display_name'] ?? '' ?></span>
    </div>

</header>
  

        <section class="admin-page">
            
<header class="content-header">
  <div>
    <h2>Events</h2>
    <p>View, edit, and create events.</p>
  </div>

  <div class="actions">
    <a href="/admin/events/create" class="btn btn-primary">+ Create Event</a>
  </div>
</header>

<form method="get" action="/admin/events" class="filters">
  <div class="filters-grid">
    <div>
      <label for="search_query">Search</label>
      <input id="search_query" type="text" name="search_query"
        value="<?= htmlspecialchars($query['search_query'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        placeholder="Search event...">
    </div>

    <div>
      <label for="datestart">Date Start</label>
      <input id="datestart" type="datetime-local" name="datestart"
        value="<?= htmlspecialchars($query['datestart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="dateend">Date End</label>
      <input id="dateend" type="datetime-local" name="dateend"
        value="<?= htmlspecialchars($query['dateend'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="recurring">Recurring</label>
      <select id="recurring" name="recurring">
        <?php $rec = $query['recurring'] ?? ''; ?>
        <option value="" <?= $rec === '' ? 'selected' : '' ?>>All</option>
        <option value="never" <?= $rec === 'never' ? 'selected' : '' ?>>Never</option>
        <option value="daily" <?= $rec === 'daily' ? 'selected' : '' ?>>Daily</option>
        <option value="weekly" <?= $rec === 'weekly' ? 'selected' : '' ?>>Weekly</option>
        <option value="monthly" <?= $rec === 'monthly' ? 'selected' : '' ?>>Monthly</option>
        <option value="yearly" <?= $rec === 'yearly' ? 'selected' : '' ?>>Yearly</option>
      </select>
    </div>

    <div>
      <label for="status">Status</label>
      <select id="status" name="status">
        <?php $st = $query['status'] ?? ''; ?>
        <option value="" <?= $st === '' ? 'selected' : '' ?>>All</option>
        <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="upcoming" <?= $st === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
        <option value="ended" <?= $st === 'ended' ? 'selected' : '' ?>>Ended</option>
      </select>
    </div>

    <div>
      <label for="page_id">Page ID</label>
      <input id="page_id" type="number" name="page_id"
        value="<?= htmlspecialchars($query['page_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>
  </div>

  <!-- keep current sort while filtering -->
  <input type="hidden" name="sort" value="<?= htmlspecialchars($query['sort'] ?? 'id', ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="dir" value="<?= htmlspecialchars($query['dir'] ?? 'asc', ENT_QUOTES, 'UTF-8') ?>">

  <div class="filters-actions">
    <button type="submit" class="btn btn-primary">Apply</button>
    <a href="/admin/events" class="btn">Reset</a>
  </div>
</form>

<?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/table.html', [
  'headers'  => $headers,
  'rows'     => $rows,
  'sortable' => $sortable,
  'query'    => $query,
  'bulk'     => $bulk
]); ?>

<?php
  $page  = (int)($meta['page'] ?? 1);
  $pages = (int)($meta['pages'] ?? 1);

  // preserve current query on pagination
  $baseQuery = $query;
?>

<nav class="pagination" aria-label="Pagination">
  <span>Page <?= $page ?> of <?= $pages ?></span>

  <div class="actions">
    <?php if ($page > 1): ?>
      <?php $baseQuery['page'] = $page - 1; ?>
      <a class="btn" href="?<?= http_build_query($baseQuery) ?>">Prev</a>
    <?php endif; ?>

    <?php if ($page < $pages): ?>
      <?php $baseQuery['page'] = $page + 1; ?>
      <a class="btn" href="?<?= http_build_query($baseQuery) ?>">Next</a>
    <?php endif; ?>
  </div>
</nav>


        </section>
    </main>
</div> 


  
<a id="scrolltop" href="#" title="Back to top" style="display:none;"></a>

  
<!-- JS includes -->
 <script>
setTimeout(() => {
  document.querySelectorAll('.flash').forEach(el => el.remove());
}, 3000);
</script>


</body> 
</html>


















