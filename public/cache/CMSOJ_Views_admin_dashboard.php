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
  

  
<h1>Welcome to Admin </h1>
<p>working ! <?php echo \CMSOJ\Template::asset($display_name) ?> </p>
<a href="/admin/logout">logout</a>


  
<a id="scrolltop" href="#" title="Back to top" style="display: none;"></a>

<!-- <script src='<?php echo \CMSOJ\Template::asset("/assets/js/main.js") ?>''></script> -->

  
</body> 
</html>






