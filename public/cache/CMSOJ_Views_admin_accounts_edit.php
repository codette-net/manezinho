<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> Accounts  | CMSOJ </title>
  
<link rel="stylesheet" href='<?= \CMSOJ\Template::asset("/assets/css/admin.css") ?>' />
<noscript>
  <link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/noscript.css"); ?>' />
</noscript>

</head>

<body class="<?php echo $body_class ?? ''; ?>">
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
  

        <div class="admin-page">
            
<h1>Edit Account</h1>
<h2><?php echo $account['display_name']; ?> | <?php echo $account['email']; ?></h2>

<form method="POST" action="/admin/accounts/edit/<?php echo $account['id']; ?>">
  <!-- include CMSOJ/Views/components/csrf.html  -->
  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
  'label' => 'Name',
  'name' => 'name',
  'id' => 'name',
  'value' => $old['name'] ?? $account['name'] ?? '' ,
  'error' => $errors['name'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
  'label' => 'Display name',
  'name' => 'display_name',
  'id' => 'display_name',
  'value' => $old['display_name'] ?? $account['display_name'] ?? '',
  'error' => $errors['display_name'] ?? null
  ]); ?>

<?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
  'label' => 'Email',
  'name' => 'email',
  'id' => 'email',
  'type' => 'email',
  'value' => $old['email'] ?? $account['email'] ?? '',
  'error' => $errors['email'] ?? null
  ]); ?>

  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/input.html', [
  'label' => 'Password',
  'name' => 'password',
  'id' => 'password',
  'type' => 'password',
  'placeholder' => 'Leave empty to keep current password',
  'error' => $errors['password'] ?? null
  ]); ?>
  <?php if (\CMSOJ\Helpers\Permissions::can('accounts.update_role')) : ?>
  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/admin/form/select.html', [
  'label' => 'Role',
  'name' => 'role',
  'id' => 'role',
  'options' => [
  'Admin' => 'Admin',
  'User' => 'User'
  ],
  'value' => $old['role'] ?? $account['role'] ?? '',
  'error' => $errors['role'] ?? null
  ]); ?>
  <?php endif ?>

  <button type="submit" class="btn btn-primary">Update Account</button>
</form>






        </div>
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

















