<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manezinho |  <?php echo $title; ?>  </title>
  
<meta name="description"
  content="Welcome to Art Restaurant Manezinho, a unique dining experience in São Jorge, Azores. Enjoy exquisite cuisine in an artistic setting. Book your table now!">

  

<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/main.css"); ?>' />
<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/components.css"); ?>'>
<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/style.css"); ?>'>
<noscript>
  <link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/noscript.css"); ?>' />
</noscript>

<link rel="stylesheet" href="/assets/css/menu.css">

  <!-- here is the end of head  -->
</head>


<body class="">
  

  

<?php \CMSOJ\Template::partial('nav'); ?>
<main>

  <header class="header-alt">
    <h1 class="hero-header">Art Restaurant Manezinho</h1>
    <h2 class="hero-header-h2">Menu</h2>

    <div class="lang-switch">
      <a href="?lang=en" class="<?= $lang === 'en' ? 'active' : '' ?>">English</a>
      <a href="?lang=pt" class="<?= $lang === 'pt' ? 'active' : '' ?>">Português</a>
    </div>
    <div class="sub-menu-nav">
      <ul>
      <?php foreach ($tree as $main): ?>
      <li>
        <a href="/menu#<?= htmlspecialchars($main["name_$lang"] ?: $main["name_en"])?>">
          <?= htmlspecialchars($main["name_$lang"] ?: $main["name_en"])?>
        </a>
      </li>
      <?php endforeach; ?>
      </ul>

    </div>
  </header>



  <?php foreach ($tree as $main): ?>
  <section class="menu-section">
    <h2 id="<?= htmlspecialchars($main["name_$lang"] ?: $main["name_en"])?>">
      <?= htmlspecialchars($main["name_$lang"] ?: $main["name_en"]) ?>
    </h2>

    <?php if ($main["description_$lang"]): ?>
    <p>
      <?= nl2br(htmlspecialchars($main["description_$lang"])) ?>
    </p>
    <?php endif; ?>

    <?php if (!empty($main['children'])): ?>
    <?php foreach ($main['children'] as $sub): ?>
    <article class="menu-subsection">
      <h3>
        <?= htmlspecialchars($sub["name_$lang"] ?: $sub["name_en"]) ?>
      </h3>
      <?php if ($sub["description_$lang"]): ?>
      <p>
        <?= nl2br(htmlspecialchars($sub["description_$lang"])) ?>
      </p>
      <?php endif; ?>
      <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/menu-table.html', [
      'id' => $sub['id'],
      'items' => $itemsBySection,
      'lang' => $lang
      ]); ?>


    </article>
    <?php endforeach; ?>
    <?php else: ?>
    <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/menu-table.html', ['id' => $main['id'], 'items' => $itemsBySection, 'lang' => $lang]); ?>

    <?php endif; ?>

  </section>
  <?php endforeach; ?>

  </div>

  <?php \CMSOJ\Template::partial('reservation'); ?>

</main>



  
<a id="scrolltop" href="#" title="Back to top" style="display: none;"></a>

  
<script src='<?php echo \CMSOJ\Template::asset("/assets/js/main.js"); ?>''></script>
<script src='<?php echo \CMSOJ\Template::asset("/assets/js/reservation.js"); ?>''></script>

</body>

</html>



















<?php \CMSOJ\Template::partial('footer'); ?>