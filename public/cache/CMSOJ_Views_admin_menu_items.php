<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> Menu Items  | CMSOJ </title>
  
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
    <h2>Menu Items</h2>
    <p>Quickly manage multilingual items, prices and visibility.</p>
  </div>

  <div class="actions">
    <a href="/admin/menu/items/create" class="btn btn-primary">+ New Item</a>
  </div>
</header>

<form method="get" class="filter-bar">
  <label>
    Section
    <select name="section_id">
      <?php foreach ($sectionsFilter as $val => $label): ?>
        <option value="<?= htmlspecialchars($val) ?>"
          <?= (isset($query['section_id']) && (string)$query['section_id'] === (string)$val) ? 'selected' : '' ?>>
          <?= htmlspecialchars($label) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Status
    <select name="status">
      <option value="all" <?= ($query['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All</option>
      <option value="1"   <?= ($query['status'] ?? 'all') === '1'   ? 'selected' : '' ?>>Active</option>
      <option value="0"   <?= ($query['status'] ?? 'all') === '0'   ? 'selected' : '' ?>>Inactive</option>
    </select>
  </label>

  <label>
    Search
    <input type="text" name="search" value="<?= htmlspecialchars($query['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Search name or description">
  </label>

  <button type="submit" class="btn">Filter</button>
</form>

<?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/table.html', [
  'headers'  => $headers,
  'rows'     => $rows,
  'sortable' => $sortable,
  'query'    => $query,
  'bulk'     => $bulk
]); ?>

<script>
  async function menuItemUpdateInline(id, field, value) {
    const res = await fetch('/admin/menu/items/update-inline', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ id, field, value })
    });

    try {
      const data = await res.json();
      return !!data.success;
    } catch (e) {
      return false;
    }
  }

  document.addEventListener('change', async (e) => {
    const target = e.target;
    if (!target.matches('.inline-edit, .toggle-active')) return;

    const tr = target.closest('tr');
    if (!tr) return;

    const id    = tr.dataset.id;
    const field = target.dataset.inline;
    let value;

    if (!field) return;

    if (target.type === 'checkbox') {
      value = target.checked ? 1 : 0;
    } else {
      value = target.value;
    }

    const ok = await menuItemUpdateInline(id, field, value);

    if (ok && !target.type === 'checkbox') {
      target.style.background = '#d1ffd1';
      setTimeout(() => target.style.background = '', 800);
    }
  });
</script>

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


















