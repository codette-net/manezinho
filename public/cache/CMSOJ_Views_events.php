<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<meta property="og:title" content="Art Restaurant Manezinho" />
<meta property="og:locale" content="en_US" />
<meta property="og:locale:alternate" content="pt_PT" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.artrestaurantmanezinho.com" />
<meta property="og:image" content="https://www.artrestaurantmanezinho.com/assets/img/manezinhooutside1024.JPG" />
<meta property="og:image:alt" content="Outside bird view of Manezinho art Restaurant" />
<meta property="og:description" content="Art restaurant Manezinho is a place dedicated to good food, art and music." />
<!-- <meta property="og:video" content="https://www.artrestaurantmanezinho.com/assets/mp4/main.mp4" /> -->
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<meta name="rating" content="general">
<meta name="revisit-after" content="7 days">
<script async src="https://www.googletagmanager.com/gtag/js?id=G-6Y85J3B80J"></script>


<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />

<meta name="description" content="Welcome to Art Restaurant Manezinho...">

<script>
  window.dataLayer = window.dataLayer || [];
  function gtag() { dataLayer.push(arguments); }
  gtag('js', new Date());

  gtag('config', 'G-6Y85J3B80J');
</script>


<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/main.css"); ?>' />
<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/components.css"); ?>'>
<link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/style.css"); ?>'>
<noscript>
  <link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/noscript.css"); ?>' />
</noscript>

<link rel="stylesheet" href="/assets/css/calendar.css">

<title>Manezinho | <?php echo $title; ?> </title>
<link rel="apple-touch-icon" sizes="180x180" href='<?php echo \CMSOJ\Template::asset("/assets/img/favicon/apple-touch-icon.png"); ?>'>
<link rel="icon" type="image/png" sizes="32x32" href='<?php echo \CMSOJ\Template::asset("/assets/img/favicon/favicon-32x32.png"); ?>'>
<link rel="icon" type="image/png" sizes="16x16" href='<?php echo \CMSOJ\Template::asset("/assets/img/favicon/favicon-16x16.png"); ?>'>
<link rel="manifest" href='<?php echo \CMSOJ\Template::asset("/assets/img/favicon/site.webmanifest"); ?>'>
</head>


<body class="">
  

  
<?php \CMSOJ\Template::partial('nav'); ?>

<main>
    <header class="header-alt">
      <h1 class="hero-header">Art Restaurant Manezinho</h1>
      <h2 class="hero-header-h2">Events</h2>
      <p>
        Enjoy live music in a relaxed, welcoming setting. Manezinho hosts regular performances by local and visiting
        musicians, with styles that range from blues and jazz to acoustic nights and spontaneous jam sessions. Feeling
        musical yourself? Join one of our open stage evenings and share the spotlight.
      </p>
      <p>
        <a href="#events" class="button primary">Coming events</a>
      </p>


    </header>
    <section class="panel">
      <div class="col">
        <h2 class="major">Resident Musicians</h2>
        <p>

          Manezinho has its own circle of regular performers who help shape the atmosphere of our live music nights. One
          of them is Ewout “Ewi” Adriaans, often seen on stage with his own repertoire or joining others on guitar,
          drums or harmonica. Each resident brings their own style, creating evenings that feel both familiar and full
          of surprises.</p>
      </div>
      <div class="col">
        <figure class="image img-max shadow-low">
          <img src="./assets/img/ewiHarmonica.jpg" alt="Ewi playing harmonica at Manezinho" loading="lazy">
          <figcaption>Ewout on harmonica</figcaption>
        </figure>
      </div>
    </section>
    <section class="panel col-rev">
      <div class="col">
        <figure class="image img-max shadow-low">
          <img src="./assets/img/pieterJet.png" alt="Pieter and Jet performing at Manezinho" loading="lazy">
          <figcaption>Pieter and Jet on stage</figcaption>
        </figure>
      </div>
      <div class="col">
        <h2 class="major">
          Guest Performances
        </h2>
        <p>
          Alongside our resident musicians, Manezinho hosts guest artists who bring new sounds and styles to our stage.
          These performances range from acoustic sets to blues, jazz and spontaneous collaborations with our regulars.
          And if the evening is just right, you might even catch the Adriaans Brothers joining in for an unforgettable
          blues session.
        </p>
      </div>
    </section>

    <section class="panel">
      <div class="col">
        <h2 class="major">Open stage</h2>
        <p>Want to join in? Our open stage nights invite musicians of all levels to share a song, jam with others or
          simply enjoy the music from a different perspective.</p>
        <p>
          Check our events calendar for upcoming open stage nights or send us a message to express your interest in
          performing.
          <a href="mailto:info@artrestaurantmanezinho.com">info@artrestaurantmanezinho.com</a>
        </p>

      </div>
      <div class="col">
        <figure class="image img-max shadow-low">
          <img src="./assets/img/impressionHeroImg.jpg" alt="Collage of events at Manezinho" loading="lazy">

        </figure>
      </div>
    </section>
    <section id="events">

      <header class="header-alt">
        <h2 class="hero-header-h2">Events Calendar</h2>
      </header>
      <div class="calendar-container"></div>



    </section>
  <?php \CMSOJ\Template::partial('reservation'); ?>

   
  </main>

  

  
<a id="scrolltop" href="#" title="Back to top" style="display: none;"></a>

  

<script src='<?php echo \CMSOJ\Template::asset("/assets/js/main.js"); ?>''></script>
<script src=' <?php echo \CMSOJ\Template::asset("/assets/js/reservation.js"); ?>''></script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Restaurant",
  "name": "Art Restaurant Manezinho",
      "description": "Art restaurant Manezinho is a place dedicated to good food, art and music.",

  "image": "https://www.artrestaurantmanezinho.com/assets/img/manezinhooutside1024.JPG",
  "address": {
   "@type": "PostalAddress",
        "addressLocality": "Sao Jorge, Azores",
        "addressRegion": "Urzelina",
        "streetAddress": "Canada do Açougue, 9800"
    "addressCountry": "Portugal"
  },
  "telephone": "+3512954140963",
  "url": "https://www.artrestaurantmanezinho.com",
  "servesCuisine": "Azorean, Seafood, Portuguese",
  "priceRange": "€€",
  
  "sameAs": [
    "https://www.facebook.com/artrestaurantmanezinho"
  ]
}
</script>

<script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@type": "Restaurant",
      "name": "Art restaurant Manezinho",
      "description": "Art restaurant Manezinho is a place dedicated to good food, art and music.",
      "openingHours": "We,Th,Fr,Sa,Su 18:30-02:00",
      "telephone": "+351295414096"
    }
  </script>

<script src="/assets/js/Calendar.js"></script>
<!-- <script src="CMSOJ/Views/js/events.js"></script> -->
 <script>
   new Calendar({
      // Unique ID - each page should have a unique ID
      uid: 1,
      // Size of the calendar - normal | mini | auto
      size: 'mini',
      // Display calendar - true | false
      display_calendar: true,
      // Expanded list - true | false
      expanded_list: true
    });
</script>

</body>

</html>



















<?php \CMSOJ\Template::partial('footer'); ?>


