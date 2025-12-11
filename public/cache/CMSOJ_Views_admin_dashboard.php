<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>
    <?php echo \CMSOJ\Template::asset($title ?? 'Dashboard') ?>
 | CMSOJ </title>
  
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


  
<div class="dashboard-grid">

    <div class="stat-box">
        <h3>Total Accounts</h3>
        <span><?= $totalAccounts ?></span>
    </div>

    <div class="stat-box">
        <h3>Total Messages</h3>
        <span><?= $totalMessages ?></span>
    </div>

    <div class="stat-box">
        <h3>Unread Messages</h3>
        <span><?= $unreadMessages ?></span>
    </div>

    <div class="stat-box">
        <h3>Total Events</h3>
        <span><?= $totalEvents ?></span>
    </div>

</div>


  
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

