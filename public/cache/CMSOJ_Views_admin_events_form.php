<?php class_exists('CMSOJ\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> | CMSOJ </title>
  
<link rel="stylesheet" href='<?= \CMSOJ\Template::asset("/assets/css/classless.css") ?>' />
<link rel="stylesheet" href='<?= \CMSOJ\Template::asset("/assets/css/admin_new.css") ?>' />

<noscript>
  <link rel="stylesheet" href='<?php echo \CMSOJ\Template::asset("/assets/css/noscript.css"); ?>' />
</noscript>

</head>

<body class="<?php echo $body_class ?? 'admin-main'; ?>">
  <?php echo CMSOJ\Template::renderComponent('CMSOJ/Views/components/flash.html', []); ?>

  

  
  <?php \CMSOJ\Template::partial('CMSOJ/Views/admin/partials/header.html'); ?>

  <section class="admin-head">
    <div>
      <h1><?php echo $title; ?></h1>
      <p class="muted"><?php if editing ?>Update the event details.<?php else ?>Create a new event.<?php endif ?></p>
    </div>
    <div class="admin-actions">
      <a class="btn alt" href="/admin/events">Cancel</a>
    </div>
  </section>

  <form method="post" action="/admin/events/save" enctype="multipart/form-data" class="form">
    <input type="hidden" name="_csrf" value="<?php echo $csrf; ?>">
    <?php if editing ?>
      <input type="hidden" name="id" value="<?php echo event.id; ?>">
    <?php endif ?>

    <div class="grid">
      <div>
        <label for="uid">Page ID</label>
        <input id="uid" name="uid" type="number" value="<?php echo event.uid; ?>">
      </div>

      <div>
        <label for="title">Title <span aria-hidden="true">*</span></label>
        <input id="title" name="title" required type="text" value="<?php echo event.title; ?>">
      </div>

      <div class="col-span-2">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5"><?php echo event.description; ?></textarea>
      </div>

      <div>
        <label for="datestart">Start Date <span aria-hidden="true">*</span></label>
        <input id="datestart" name="datestart" required type="datetime-local" value="<?php echo event.datestart; ?>">
      </div>

      <div>
        <label for="dateend">End Date <span aria-hidden="true">*</span></label>
        <input id="dateend" name="dateend" required type="datetime-local" value="<?php echo event.dateend; ?>">
      </div>

      <div>
        <label for="color">Color</label>
        <input id="color" name="color" type="text" value="<?php echo event.color; ?>" placeholder="#2163BA">
      </div>

      <div>
        <label for="recurring">Recurring <span aria-hidden="true">*</span></label>
        <select id="recurring" name="recurring" required>
          <option value="never" <?php if event.recurring == "never" ?>selected<?php endif ?>>Never</option>
          <option value="daily" <?php if event.recurring == "daily" ?>selected<?php endif ?>>Daily</option>
          <option value="weekly" <?php if event.recurring == "weekly" ?>selected<?php endif ?>>Weekly</option>
          <option value="monthly" <?php if event.recurring == "monthly" ?>selected<?php endif ?>>Monthly</option>
          <option value="yearly" <?php if event.recurring == "yearly" ?>selected<?php endif ?>>Yearly</option>
        </select>
      </div>

      <div class="col-span-2">
        <label for="photo">Photo</label>

        <?php if event.photo_url ?>
          <figure class="preview">
            <img src="/<?php echo event.photo_url; ?>" alt="<?php echo event.title; ?>">
            <figcaption class="muted">Current image</figcaption>
          </figure>
        <?php endif ?>

        <input id="photo" name="photo" type="file" accept="image/*">
        <p class="muted">Uploading a new image will replace the existing one.</p>
      </div>

      <div class="col-span-2">
        <label for="redirect_url">Redirect URL</label>
        <input id="redirect_url" name="redirect_url" type="url" value="<?php echo event.redirect_url; ?>" placeholder="https://...">
      </div>

      <div>
        <label for="submit_date">Submit Date <span aria-hidden="true">*</span></label>
        <input id="submit_date" name="submit_date" required type="datetime-local" value="<?php echo event.submit_date; ?>">
      </div>
    </div>

    <div class="form-actions">
      <button class="btn" type="submit">Save</button>

      <?php if editing ?>
        <form method="post" action="/admin/events/delete/<?php echo event.id; ?>" style="display:inline" onsubmit="return confirm('Delete this event?');">
          <input type="hidden" name="_csrf" value="<?php echo $csrf; ?>">
          <button class="btn red" type="submit">Delete</button>
        </form>
      <?php endif ?>
    </div>
  </form>


  
<a id="scrolltop" ...></a>

  
<!-- JS includes -->
 <script>
setTimeout(() => {
  document.querySelectorAll('.flash').forEach(el => el.remove());
}, 3000);
</script>


</body> 
</html>









