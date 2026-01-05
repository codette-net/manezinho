<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> | CMSOJ </title>
  
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
            
 

  <section class="admin-head">
    <div>
      <h1>Events</h1>
      <p class="muted">View, edit, and create events.</p>
    </div>

    <div class="admin-actions">
      <a class="btn" href="/admin/events/create">Create Event</a>
    </div>
  </section>


  <form class="filters" method="get" action="/admin/events">
    <div class="grid">
      <div>
        <label for="search_query">Search</label>
        <input id="search_query" name="search_query" type="search" value="<?php echo query.search_query; ?>">
      </div>

      <div>
        <label for="datestart">Date Start</label>
        <input id="datestart" name="datestart" type="datetime-local" value="<?php echo query.datestart; ?>">
      </div>

      <div>
        <label for="dateend">Date End</label>
        <input id="dateend" name="dateend" type="datetime-local" value="<?php echo query.dateend; ?>">
      </div>

      <div>
        <label for="recurring">Recurring</label>
        <select id="recurring" name="recurring">
          <option value="" <?php if (query.recurring == ""): ?>selected<?php endif; ?>>All</option>
          <option value="never" <?php if (query.recurring == "never") : ?>selected<?php endif ?>>Never</option>
          <option value="daily" <?php if (query.recurring == "daily") : ?>selected<?php endif ?>>Daily</option>
          <option value="weekly" <?php if (query.recurring == "weekly") : ?>selected<?php endif ?>>Weekly</option>
          <option value="monthly" <?php if (query.recurring == "monthly") : ?>selected<?php endif ?>>Monthly</option>
          <option value="yearly" <?php if( query.recurring == "yearly") : ?>selected<?php endif ?>>Yearly</option>
        </select>
      </div>

      <div>
        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="" <?php if (query.status == "" ) : ?>selected<?php endif ?>>All</option>
          <option value="active" <?php if (query.status == "active") : ?>selected<?php endif ?>>Active</option>
          <option value="upcoming" <?php if (query.status == "upcoming") : ?>selected<?php endif ?>>Upcoming</option>
          <option value="ended" <?php if( query.status == "ended") : ?>selected<?php endif ?>>Ended</option>
        </select>
      </div>

      <div>
        <label for="page_id">Page ID</label>
        <input id="page_id" name="page_id" type="number" value="<?php echo query.page_id; ?>">
      </div>
    </div>

    <div class="filter-actions">
      <button class="btn" type="submit">Apply</button>
      <a class="btn alt" href="/admin/events">Reset</a>
    </div>

    <!-- keep sort state -->
    <input type="hidden" name="order" value="<?php echo meta.order; ?>">
    <input type="hidden" name="order_by" value="<?php echo meta.order_by; ?>">
  </form>

  <div class="table-wrap" role="region" aria-label="Events table" tabindex="0">
    <table class="table">
      <thead>
        <tr>
          <?php foreach($headers as $key => $label) : ?>
            <th scope="col"><?php echo $label; ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>

      <tbody>
        <?php if (rows|length == 0) : ?>
          <tr>
            <td colspan="9" class="muted">There are no events.</td>
          </tr>
        <?php endif ?>

        <?php foreach($rows as $row): ?>
          <tr>
            <?php foreach($row.cells as $cell): ?>
              <td><?php echo htmlentities($cell, ENT_QUOTES, 'UTF-8') ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <nav class="pagination" aria-label="Pagination">
    <div>
      <span class="muted">Page <?php echo meta.page; ?> of <?php echo meta.pages; ?></span>
      <span class="muted">· Total <?php echo meta.total; ?></span>
    </div>

    <div class="pager-buttons">
      <?php if (meta.page > 1): ?>
        <a class="btn alt" href="/admin/events?search_query=<?php echo query.search_query; ?>&recurring=<?php echo query.recurring; ?>&datestart=<?php echo query.datestart; ?>&dateend=<?php echo query.dateend; ?>&status=<?php echo query.status; ?>&page_id=<?php echo query.page_id; ?>&order=<?php echo meta.order; ?>&order_by=<?php echo meta.order_by; ?>&page=<?php echo meta.page - 1; ?>
">Prev</a>
      <?php endif ?>

      <?php if (meta.page < meta.pages): ?>
        <a class="btn alt" href="/admin/events?search_query=<?php echo query.search_query; ?>&recurring=<?php echo query.recurring; ?>&datestart=<?php echo query.datestart; ?>&dateend=<?php echo query.dateend; ?>&status=<?php echo query.status; ?>&page_id=<?php echo query.page_id; ?>&order=<?php echo meta.order; ?>&order_by=<?php echo meta.order_by; ?>&page=<?php echo meta.page + 1; ?>">Next</a>
      <?php endif ?>
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
















