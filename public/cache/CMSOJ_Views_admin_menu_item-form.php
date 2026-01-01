<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>
  <?php echo $editing ? 'Edit Menu Item' : 'Create Menu Item'; ?>
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
            
<h1><?php echo $editing ? 'Edit Menu Item' : 'Create Menu Item'; ?></h1>

<form method="POST" action="<?php echo $editing ? '/admin/menu/items/update/' . $item['id'] : '/admin/menu/items/store'; ?>">
  <input type="hidden" name="_csrf" value="<?= \CMSOJ\Helpers\Csrf::token() ?>">

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/select.html', [
    'label'   => 'Section',
    'name'    => 'section_id',
    'id'      => 'section_id',
    'options' => $sectionOptions,
    'value'   => $old['section_id'] ?? ($item['section_id'] ?? ''),
    'error'   => $errors['section_id'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/select.html', [
    'label'   => 'Display Type',
    'name'    => 'display_type',
    'id'      => 'display_type',
    'options' => [
      'item'    => 'Normal Item',
      'thead'   => 'Header Row (Table Head)',
      'th'      => 'Subheader Row (Inside Table)',
      'divider' => 'Divider (Colspan)',
    ],
    'value'   => $old['display_type'] ?? ($item['display_type'] ?? 'item'),
    'error'   => $errors['display_type'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Name (EN)',
    'name'  => 'name_en',
    'id'    => 'name_en',
    'value' => $old['name_en'] ?? ($item['name_en'] ?? ''),
    'error' => $errors['name_en'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Name (PT)',
    'name'  => 'name_pt',
    'id'    => 'name_pt',
    'value' => $old['name_pt'] ?? ($item['name_pt'] ?? ''),
    'error' => $errors['name_pt'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/textarea.html', [
    'label' => 'Description (EN)',
    'name'  => 'description_en',
    'id'    => 'description_en',
    'value' => $old['description_en'] ?? ($item['description_en'] ?? ''),
    'error' => $errors['description_en'] ?? null,
    'rows'  => 3
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/textarea.html', [
    'label' => 'Description (PT)',
    'name'  => 'description_pt',
    'id'    => 'description_pt',
    'value' => $old['description_pt'] ?? ($item['description_pt'] ?? ''),
    'error' => $errors['description_pt'] ?? null,
    'rows'  => 3
  ]); ?>

  <h3>Units & Prices</h3>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Unit 1',
    'name'  => 'unit_1_label',
    'id'    => 'unit_1_label',
    'value' => $old['unit_1_label'] ?? ($item['unit_1_label'] ?? ''),
    'error' => $errors['unit_1_label'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Price 1',
    'name'  => 'price_1',
    'id'    => 'price_1',
    'value' => $old['price_1'] ?? ($item['price_1'] ?? ''),
    'error' => $errors['price_1'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Unit 2',
    'name'  => 'unit_2_label',
    'id'    => 'unit_2_label',
    'value' => $old['unit_2_label'] ?? ($item['unit_2_label'] ?? ''),
    'error' => $errors['unit_2_label'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Price 2',
    'name'  => 'price_2',
    'id'    => 'price_2',
    'value' => $old['price_2'] ?? ($item['price_2'] ?? ''),
    'error' => $errors['price_2'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
    'label' => 'Sort Order',
    'name'  => 'sort_order',
    'id'    => 'sort_order',
    'type'  => 'number',
    'value' => $old['sort_order'] ?? ($item['sort_order'] ?? 0),
    'error' => $errors['sort_order'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/toggle.html', [
    'label'       => 'Active',
    'name'        => 'is_active',
    'id'          => 'is_active',
    'value'     => ($old['is_active'] ?? $item['is_active'] ?? 1) ? true : false,
    'description' => 'Visible on the public menu'
  ]); ?>

  <button type="submit" class="btn btn-primary">Save Item</button>
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


















