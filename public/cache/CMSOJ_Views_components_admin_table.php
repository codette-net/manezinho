<?php class_exists('CMSOJ\Template') or exit; ?>
<table>
  <thead>
    <tr>
      <?php foreach ($headers as $head): ?>
         <th><?php echo $head; ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($rows as $row): ?>
      <tr>
        <?php foreach ($row as $cell): ?>
          <td><?php echo $cell; ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
