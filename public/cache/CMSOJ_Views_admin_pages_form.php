<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> <?= htmlspecialchars($title ?? 'Page', ENT_QUOTES, 'UTF-8') ?>  | CMSOJ </title>
  
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
    <h2><?= htmlspecialchars($title ?? 'Page', ENT_QUOTES, 'UTF-8') ?></h2>
    <p><?= !empty($editing) ? 'Update the page details.' : 'Create a new page.' ?></p>
  </div>

  <div class="actions">
    <a href="/admin/pages" class="btn">Cancel</a>
  </div>
</header>

<?php if (!empty($flash['error'])): ?>
  <div class="msg error" role="alert"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form method="post" action="/admin/pages/save">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="id" value="<?= (int)($page['id'] ?? 0) ?>">

  <div class="form-grid">
    <div class="col-span-2">
      <label for="title">Title <span class="required">*</span></label>
      <input id="title" name="title" required type="text"
        value="<?= htmlspecialchars($page['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="col-span-2">
      <label for="slug">Slug</label>
      <p class="comment">Optional. If empty, it will be generated from the title.</p>
      <input id="slug" name="slug" type="text"
        value="<?= htmlspecialchars($page['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        placeholder="my-page">
    </div>

    <div class="col-span-2">
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="5"><?= htmlspecialchars($page['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div class="col-span-2">
      <label for="url">URL</label>
      <input id="url" name="url" type="text"
        value="<?= htmlspecialchars($page['url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        placeholder="https://...">
    </div>

    <div>
      <label>
        <input type="checkbox" name="is_active" <?= !empty($page['is_active']) ? 'checked' : '' ?>>
        Active
      </label>
    </div>
  </div>

  <div class="actions" style="margin-top: 16px;">
    <button class="btn btn-primary" type="submit">Save</button>
  </div>
</form>

<?php if (!empty($editing) && !empty($page['id'])): ?>
  <form method="post" action="/admin/pages/delete/<?= (int)$page['id'] ?>"
        onsubmit="return confirm('Delete this page?');"
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


















