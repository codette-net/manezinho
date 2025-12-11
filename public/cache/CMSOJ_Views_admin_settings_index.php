<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> Settings  | CMSOJ </title>
  
<link rel="stylesheet" href='<?= \CMSOJ\Template::asset("/assets/css/admin.css") ?>' />
<noscript>
  <link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/noscript.css") ?>' />
</noscript>

  <!-- here is the end of head  -->
</head>

<body class="<?php echo \CMSOJ\Template::asset($body_class ?? '') ?>">
  
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="logo">CMSOJ Admin</div>

<nav class="menu">
    <a href="/admin" class="<?= $selected === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="/admin/accounts" class="<?= $selected === 'accounts' ? 'active' : '' ?>">Accounts</a>
    <a href="/admin/events" class="<?= $selected === 'events' ? 'active' : '' ?>">Events</a>
    <a href="/admin/messages" class="<?= $selected === 'messages' ? 'active' : '' ?>">Messages</a>
    <a href="/admin/settings" class="<?= $selected === 'settings' ? 'active' : '' ?>">Settings</a>
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


  
<h1>Settings</h1>

<form method="POST" action="/admin/settings">

    <label>Restaurant Name</label>
    <input type="text" name="restaurant_name" value="<?php echo \CMSOJ\Template::asset($settings['site_name']) ?>">

    <label>Contact Email</label>
    <input type="email" name="contact_email" value="<?php echo \CMSOJ\Template::asset($settings['contact_email']) ?>">

    <label>Default Language</label>
    <select name="default_lang">
        <option value="en" <?php echo \CMSOJ\Template::asset($settings['default_lang']=='en'?'selected':'') ?>>English</option>
        <option value="pt" <?php echo \CMSOJ\Template::asset($settings['default_lang']=='pt'?'selected':'') ?>>Português</option>
    </select>

    <label>Reservation Max Persons</label>
    <input type="number" name="reservation_max_persons" min="1" max="50" value="<?php echo \CMSOJ\Template::asset($settings['reservation_max_persons']) ?>">

    <button type="submit">Save Settings</button>

</form>


  
        </div> {# .admin-page #}
    </main>
</div>

<a id="scrolltop" href="#" title="Back to top" style="display:none;"></a>

  
</body> 
</html>




{# <title> tag content #}


{# Sidebar + header wrapper #}


{# Main page content #}


{# Close wrappers + extra footer stuff #}






