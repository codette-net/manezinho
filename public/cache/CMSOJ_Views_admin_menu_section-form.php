<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>
  <?php echo $editing ? 'Edit Section' : 'Create Section'; ?>
 | CMSOJ </title>
  
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
            
<h1><?php echo $editing ? 'Edit Section' : 'Create Section'; ?></h1>

<form method="POST" action="/admin/menu/sections/save">
  <input type="hidden" name="_csrf" value="<?= \CMSOJ\Helpers\Csrf::token() ?>">

  <?php if (!empty($section['id'])): ?>
    <input type="hidden" name="id" value="<?= (int)$section['id'] ?>">
  <?php endif; ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/select.html', [
    'label'   => 'Parent Section',
    'name'    => 'parent_id',
    'id'      => 'parent_id',
    'options' => $parentOptions,
    'value'   => $section['parent_id'] ?? ($old['parent_id'] ?? ''),
    'error'   => $errors['parent_id'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Name (EN)',
    'name'  => 'name_en',
    'id'    => 'name_en',
    'value' => $old['name_en'] ?? ($section['name_en'] ?? ''),
    'error' => $errors['name_en'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Name (PT)',
    'name'  => 'name_pt',
    'id'    => 'name_pt',
    'value' => $old['name_pt'] ?? ($section['name_pt'] ?? ''),
    'error' => $errors['name_pt'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/textarea.html', [
    'label' => 'Description (EN)',
    'name'  => 'description_en',
    'id'    => 'description_en',
    'value' => $old['description_en'] ?? ($section['description_en'] ?? ''),
    'error' => $errors['description_en'] ?? null,
    'rows'  => 3
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/textarea.html', [
    'label' => 'Description (PT)',
    'name'  => 'description_pt',
    'id'    => 'description_pt',
    'value' => $old['description_pt'] ?? ($section['description_pt'] ?? ''),
    'error' => $errors['description_pt'] ?? null,
    'rows'  => 3
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Sort Order',
    'name'  => 'sort_order',
    'id'    => 'sort_order',
    'type'  => 'number',
    'value' => $old['sort_order'] ?? ($section['sort_order'] ?? 0),
    'error' => $errors['sort_order'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/toggle.html', [
    'label' => 'Active',
    'name'  => 'is_active',
    'id'    => 'is_active',
    'value' => ($old['is_active'] ?? $section['is_active'] ?? 1) ? true : false,
    'description' => 'Visible in the public menu'
  ]); ?>

  <button type="submit" class="btn btn-primary">Save Section</button>
</form>

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


















