<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manezinho | <?php echo $title; ?> </title>
  
  <meta name="description" content="Welcome to Art Restaurant Manezinho, a unique dining experience in São Jorge, Azores. Enjoy exquisite cuisine in an artistic setting. Book your table now!">

  
<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/main.css"); ?>' />
<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/components.css"); ?>'>
<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/style.css"); ?>'>
<noscript>
  <link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/noscript.css"); ?>' />
</noscript>

  <!-- here is the end of head  -->
</head>


<body class="">
  

  

<?php \CMSOJ\Template::partial('nav'); ?>
  <main>
    <header class="header-alt">
      <h1 class="hero-header">Art Restaurant Manezinho</h1>
      <h2 class="hero-header-h2">Flavours</h2>
      <p>
        Discover the authentic taste of São Jorge, where local ingredients and traditional island cooking inspire every
        dish. From its famous cheeses and rich dairy heritage to fresh seafood and locally raised meats, the island
        offers flavours rooted in simplicity and quality. At Manezinho, we bring these ingredients together with care,
        creating meals that reflect the character of the Azores and the spirit of its coast.
      </p>
    </header>
    <section class="panel">
      <div class="col">
        <h2 class="major"> Fresh Catch from the Atlantic</h2>
        <p>
          The fish on our menu changes with what the ocean offers each day. Whether it’s tuna, amberjack, lapas or
          another local catch, we prepare it fresh and serve it with a short story about its origin and, if you like, a
          wine pairing chosen to match its flavour.</p>
      </div>
      <div class="col">
        <figure class="image img-max shadow-low">
          <!-- srcset  -->
          <img src="assets/img/scaledImg/manezinho_food_060-small.jpg"
            sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, (max-width: 992px) 992px, 1200px"
            srcset="assets/img/scaledImg/manezinho_food_060-xl.jpg 1200w, assets/img/scaledImg/manezinho_food_060-large.jpg 992w, assets/img/scaledImg/manezinho_food_060-medium.jpg 768w, ./assets/img/scaledImg/manezinho_food_060-small.jpg 480w"
            alt="Manezinho Tuna Dish" loading="lazy">
          <figcaption>Fresh Tuna</figcaption>
        </figure>
      </div>
    </section>
    <section class="panel col-rev">
      <div class="col">
        <figure class="image img-max shadow-low">
          <img src="assets/img/scaledImg/manezinho_food_031-small.jpg"
            sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, (max-width: 992px) 992px, 1200px"
            srcset="assets/img/scaledImg/manezinho_food_031-xl.jpg 1200w, assets/img/scaledImg/manezinho_food_031-large.jpg 992w, assets/img/scaledImg/manezinho_food_031-medium.jpg 768w, ./assets/img/scaledImg/manezinho_food_031-small.jpg 480w"
            alt="Manezinho Hamburger Dish" loading="lazy">
          <figcaption>Manezinho Hamburger</figcaption>
        </figure>
      </div>
      <div class="col">
        <h2 class="major">
          Crafted Cuts & Signature Dishes
        </h2>
        <p>
          Our selection of island meats highlights the best of local producers, featuring premium cuts prepared with
          care and served with thoughtful sides. Among them is our signature manezinho hamburger. It is made with ground
          beef from the islands, which gives it a unique and delicious flavor.
        </p>
      </div>
    </section>

    <section class="panel">
      <div class="col">
        <h2 class="major">Cheese & Tapas</h2>
        <p>São Jorge is known for its bold, flavorful cheese, and our tapas menu celebrates this local specialty
          alongside a selection of small plates made with regional ingredients. From dairy-rich bites to fresh seafood
          and cured meats, each tapa is prepared to share and enjoy at a relaxed pace.</p>
      </div>
      <div class="col">
        <figure class="image img-max shadow-low">
          <img src="assets/img/scaledImg/manezinho_food_033-small.jpg"
            sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, (max-width: 992px) 992px, 1200px"
            srcset="assets/img/scaledImg/manezinho_food_033-xl.jpg 1200w, assets/img/scaledImg/manezinho_food_033-large.jpg 992w, assets/img/scaledImg/manezinho_food_033-medium.jpg 768w, ./assets/img/scaledImg/manezinho_food_033-small.jpg 480w"
            alt="Manezinho Tapas Dish" loading="lazy">
          <figcaption>Assorted Tapas</figcaption>
        </figure>
      </div>
    </section>
    <!-- wines -->
    <section class="panel col-rev">
      <div class="col">
        <figure class="image img-max shadow-low">
          <img src="assets/img/scaledImg/manezinho_food_058-small.jpg"
            sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, (max-width: 992px) 992px, 1200px"
            srcset="assets/img/scaledImg/manezinho_food_058-xl.jpg 1200w, assets/img/scaledImg/manezinho_food_058-large.jpg 992w, assets/img/scaledImg/manezinho_food_058-medium.jpg 768w, ./assets/img/scaledImg/manezinho_food_058-small.jpg 480w"
            alt="Manezinho Wine Selection" loading="lazy">
          <figcaption>Wine Selection</figcaption>
        </figure>
      </div>
      <div class="col">
        <h2 class="major">Curated Wine Selection</h2>
        <p>
          Our wine list showcases Azorean wines defined by volcanic terroir and limited production, alongside select
          bottles from mainland regions. Let us guide you to the ideal pairing, whether you’re enjoying seafood, local
          meats or a shared plate of São Jorge cheese.
        </p>
      </div>
    </section>
  
      <!-- cocktails & gins img 036 -->
      <section class="panel">

        <div class="col">
          <h2 class="major">
            Craft Cocktails & Local Gins
          </h2>
          <p>
            Enjoy a selection of classic cocktails and carefully chosen gins, served simply and made to suit every
            taste. Whether you prefer something refreshing, smooth or bold, we can help you find the right drink to
            enjoy on its own or alongside your meal. Relax at the bar, try something familiar or discover a new
            favourite with our recommendations.

          </p>
        </div>
        <div class="col">
          <figure class="image img-max shadow-low">
            <img src="assets/img/scaledImg/manezinho_food_036-small.jpg"
              sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, (max-width: 992px) 992px, 1200px"
              srcset="assets/img/scaledImg/manezinho_food_036-xl.jpg 1200w, assets/img/scaledImg/manezinho_food_036-large.jpg 992w, assets/img/scaledImg/manezinho_food_036-medium.jpg 768w, ./assets/img/scaledImg/manezinho_food_036-small.jpg 480w"
              alt="Manezinho Cocktails and Gins" loading="lazy">
            <figcaption>Strawberry Martini</figcaption>
          </figure>
        </div>
      </section>


<?php \CMSOJ\Template::partial('reservation'); ?>
  </main>


<?php \CMSOJ\Template::partial('footer'); ?>




  
<a id="scrolltop" href="#" title="Back to top" style="display: none;"></a>

  
<script src='<?php echo \CMSOJ\Template::asset("/assets/js/main.js"); ?>''></script>
<script src='<?php echo \CMSOJ\Template::asset("/assets/js/reservation.js"); ?>''></script>

</body>

</html>













