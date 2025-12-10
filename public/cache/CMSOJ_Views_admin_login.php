<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> <?php echo \CMSOJ\Template::asset($title) ?>  | CMSOJ </title>
  
<link rel="stylesheet" href='<?= \CMSOJ\Template::asset("/assets/css/admin.css") ?>' />
<noscript>
  <link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/noscript.css") ?>' />
</noscript>

  <!-- here is the end of head  -->
</head>

<body class="<?php echo \CMSOJ\Template::asset($body_class ?? '') ?>">
  

  

<form action="/admin/login" method="post" class="">
  <h2>Admin Login</h2>
  <input type="email" name="admin_email" placeholder="Email" required>
  <input type="password" name="admin_password" placeholder="Password" required>
  <input type="submit" value="Login">
  <?php if (!empty($_SESSION['login_error'])): ?>
  <div class="error-msg">
    <?= htmlspecialchars($_SESSION['login_error']) ?>
  </div>
  <?php unset($_SESSION['login_error']); ?>
  <?php endif; ?>

  <p>
    <a href="/admin/forgot-password">Forgot Password?</a>
  </p>
</form>



  
<a id="scrolltop" href="#" title="Back to top" style="display: none;"></a>

<!-- <script src='<?php echo \CMSOJ\Template::asset("/assets/js/main.js") ?>''></script> -->

  
</body> 
</html>






