<?php class_exists('CMSOJ\Template') or exit; ?>
<table>
<thead>
<tr>
<?php foreach ($headers as $key => $label): ?>
  <th>
    <?php if (in_array($key, $sortable, true)): ?>

      <?php
        $isActive = ($query['sort'] ?? '') === $key;
        $dir = $isActive && ($query['dir'] ?? 'asc') === 'asc'
            ? 'desc'
            : 'asc';

        $icon = '';
        if ($isActive) {
          $icon = ($query['dir'] ?? 'asc') === 'asc' ? ' ↑' : ' ↓';
        }
      ?>

      <a href="?<?= http_build_query(array_merge($query, [
          'sort' => $key,
          'dir'  => $dir,
          'page' => 1
      ])) ?>">
        <?= $label . $icon ?>
      </a>

    <?php else: ?>
      <?= $label ?>
    <?php endif; ?>
  </th>
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
