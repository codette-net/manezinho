<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> <?= htmlspecialchars($title ?? 'Event', ENT_QUOTES, 'UTF-8') ?>  | CMSOJ </title>
  
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
    <h2><?= htmlspecialchars($title ?? 'Event', ENT_QUOTES, 'UTF-8') ?></h2>
    <p><?= !empty($editing) ? 'Update the event details.' : 'Create a new event.' ?></p>
  </div>

  <div class="actions">
    <a href="/admin/events" class="btn">Cancel</a>
  </div>
</header>

<?php if (!empty($flash['error'])): ?>
  <div class="msg error" role="alert"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form method="post" action="/admin/events/save" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

  <?php if (!empty($editing)): ?>
    <input type="hidden" name="id" value="<?= (int)$event['id'] ?>">
  <?php endif; ?>

  <div class="form-grid">
    <div>
      <label for="page_id">Page ID</label>
      <input id="page_id" name="page_id" type="number" value="<?= htmlspecialchars($event['page_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="title">Title <span class="required">*</span></label>
      <input id="title" name="title" required type="text" value="<?= htmlspecialchars($event['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="col-span-2">
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="6"><?= htmlspecialchars($event['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
      <label for="datestart">Start Date <span class="required">*</span></label>
      <input id="datestart" name="datestart" required type="datetime-local"
        value="<?= htmlspecialchars($event['datestart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="dateend">End Date <span class="required">*</span></label>
      <input id="dateend" name="dateend" required type="datetime-local"
        value="<?= htmlspecialchars($event['dateend'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="color">Color</label>
      <input id="color" name="color" type="text" placeholder="#2163BA"
        value="<?= htmlspecialchars($event['color'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="recurring">Recurring <span class="required">*</span></label>
      <?php $r = $event['recurring'] ?? 'never'; ?>
      <select id="recurring" name="recurring" required>
        <option value="never" <?= $r === 'never' ? 'selected' : '' ?>>Never</option>
        <option value="daily" <?= $r === 'daily' ? 'selected' : '' ?>>Daily</option>
        <option value="weekly" <?= $r === 'weekly' ? 'selected' : '' ?>>Weekly</option>
        <option value="monthly" <?= $r === 'monthly' ? 'selected' : '' ?>>Monthly</option>
        <option value="yearly" <?= $r === 'yearly' ? 'selected' : '' ?>>Yearly</option>
      </select>
    </div>

    <div class="col-span-2">
      <label for="photo">Photo</label>

      <?php if (!empty($event['photo_url'])): ?>
        <figure style="margin: 10px 0;">
          <img src="/<?= htmlspecialchars($event['photo_url'], ENT_QUOTES, 'UTF-8') ?>"
               alt="<?= htmlspecialchars($event['title'] ?? 'Event photo', ENT_QUOTES, 'UTF-8') ?>"
               style="max-width:200px; height:auto;">
          <figcaption class="muted">Current image</figcaption>
        </figure>
      <?php endif; ?>

      <input id="photo" type="file" name="photo" accept="image/*">
      <p class="muted">Uploading a new image will replace the existing one.</p>
    </div>

    <div class="col-span-2">
      <label for="redirect_url">Redirect URL</label>
      <input id="redirect_url" name="redirect_url" type="url"
        value="<?= htmlspecialchars($event['redirect_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        placeholder="https://...">
    </div>

    <div>
      <label for="submit_date">Submit Date <span class="required">*</span></label>
      <input id="submit_date" name="submit_date" required type="datetime-local"
        value="<?= htmlspecialchars($event['submit_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>
  </div>

  <div class="actions" style="margin-top: 16px;">
    <button class="btn btn-primary" type="submit">Save</button>
  </div>
</form>

<?php if (!empty($editing)): ?>
  <form method="post" action="/admin/events/delete/<?= (int)$event['id'] ?>"
        onsubmit="return confirm('Delete this event?');"
        style="margin-top: 10px;">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
    <button class="btn btn-danger" type="submit">Delete</button>
  </form>
<?php endif; ?>


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


















