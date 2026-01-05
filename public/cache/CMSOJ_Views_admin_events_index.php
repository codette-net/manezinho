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
      <h1>Events</h1>
      <p class="muted">View, edit, and create events.</p>
    </div>

    <div class="admin-actions">
      <a class="btn" href="/admin/events/create">Create Event</a>
    </div>
  </section>

  <?php if flash.success ?>
    <div class="msg success" role="status"><?php echo flash.success; ?></div>
  <?php endif ?>
  <?php if flash.error ?>
    <div class="msg error" role="alert"><?php echo flash.error; ?></div>
  <?php endif ?>

  <form class="filters" method="get" action="/admin/events">
    <div class="grid">
      <div>
        <label for="search_query">Search</label>
        <input id="search_query" name="search_query" type="search" value="<?php echo query.search_query; ?>">
      </div>

      <div>
        <label for="datestart">Date Start</label>
        <input id="datestart" name="datestart" type="datetime-local" value="<?php echo query.datestart; ?>">
      </div>

      <div>
        <label for="dateend">Date End</label>
        <input id="dateend" name="dateend" type="datetime-local" value="<?php echo query.dateend; ?>">
      </div>

      <div>
        <label for="recurring">Recurring</label>
        <select id="recurring" name="recurring">
          <option value="" <?php if query.recurring == "" ?>selected<?php endif ?>>All</option>
          <option value="never" <?php if query.recurring == "never" ?>selected<?php endif ?>>Never</option>
          <option value="daily" <?php if query.recurring == "daily" ?>selected<?php endif ?>>Daily</option>
          <option value="weekly" <?php if query.recurring == "weekly" ?>selected<?php endif ?>>Weekly</option>
          <option value="monthly" <?php if query.recurring == "monthly" ?>selected<?php endif ?>>Monthly</option>
          <option value="yearly" <?php if query.recurring == "yearly" ?>selected<?php endif ?>>Yearly</option>
        </select>
      </div>

      <div>
        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="" <?php if query.status == "" ?>selected<?php endif ?>>All</option>
          <option value="active" <?php if query.status == "active" ?>selected<?php endif ?>>Active</option>
          <option value="upcoming" <?php if query.status == "upcoming" ?>selected<?php endif ?>>Upcoming</option>
          <option value="ended" <?php if query.status == "ended" ?>selected<?php endif ?>>Ended</option>
        </select>
      </div>

      <div>
        <label for="page_id">Page ID</label>
        <input id="page_id" name="page_id" type="number" value="<?php echo query.page_id; ?>">
      </div>
    </div>

    <div class="filter-actions">
      <button class="btn" type="submit">Apply</button>
      <a class="btn alt" href="/admin/events">Reset</a>
    </div>

    <!-- keep sort state -->
    <input type="hidden" name="order" value="<?php echo meta.order; ?>">
    <input type="hidden" name="order_by" value="<?php echo meta.order_by; ?>">
  </form>

  <div class="table-wrap" role="region" aria-label="Events table" tabindex="0">
    <table class="table">
      <thead>
        <tr>
          <?php for key, label in headers ?>
            <th scope="col"><?php echo $label; ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>

      <tbody>
        <?php if rows|length == 0 ?>
          <tr>
            <td colspan="9" class="muted">There are no events.</td>
          </tr>
        <?php endif ?>

        <?php foreach ($rows as $row): ?>
          <tr>
            <?php for cell in row.cells ?>
              <td><?php echo htmlentities(cell, ENT_QUOTES, 'UTF-8') ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <nav class="pagination" aria-label="Pagination">
    <div>
      <span class="muted">Page <?php echo meta.page; ?> of <?php echo meta.pages; ?></span>
      <span class="muted">· Total <?php echo meta.total; ?></span>
    </div>

    <div class="pager-buttons">
      <?php if meta.page > 1 ?>
        <a class="btn alt" href="/admin/events?">Prev</a>
      <?php endif ?>

      <?php if meta.page < meta.pages ?>
        <a class="btn alt" href="/admin/events?">Next</a>
      <?php endif ?>
    </div>
  </nav>


  
<a id="scrolltop" ...></a>

  
<!-- JS includes -->
 <script>
setTimeout(() => {
  document.querySelectorAll('.flash').forEach(el => el.remove());
}, 3000);
</script>


</body> 
</html>









