<?php class_exists('CMSOJ\Template') or exit; ?>
<?php
$sectionItems = $items[$id] ?? [];

if (!$sectionItems) return;
?>

<table class="menu-table">
    <?php $thead_open = false; ?>

    <?php foreach ($sectionItems as $it):
 
        $type = $it['display_type'] ?: 'item';
        $name = trim($it["name_$lang"] ?? '');
        if ($name === '') {
            $name = trim($it['name_en'] ?? '');
        }
        $desc = trim($it["description_$lang"] ?? '');
        if ($desc === '') {
            $desc = trim($it['description_en'] ?? '');
        }

        $p1   = trim($it['price_1'] ?? '');
        $p2   = trim($it['price_2'] ?? '');
        $u1   = trim($it['unit_1_label'] ?? '');
        $u2   = trim($it['unit_2_label'] ?? '');

        // Headers
        if ($type === 'thead'):
            if ($thead_open) echo "</tbody>";
            echo "<thead><tr><th>$name</th></tr></thead><tbody>";
            $thead_open = true;
            continue;
        endif;

        if ($type === 'th'):
            echo "<tr><th>$name</th><th>$u1</th><th>$u2</th></tr>";
            continue;
        endif;

        if ($type === 'divider'):
            echo "<tr class='tr-divider'><th colspan='3'>$name</th></tr>";
            continue;
        endif;

        // Normal item
        echo "<tr><td>";

        if ($name && $desc) {
            if ($desc[0] !== '(') $desc = "($desc)";
            echo "$name <small><em>$desc</em></small>";
        } elseif ($name) {
            echo $name;
        } elseif ($desc) {
            echo "<small><em>$desc</em></small>";
        } else {
          echo "&nbsp;";
        }

        echo "</td>";

          // --- PRICE CELLS ---
        if ($p1 && $p2) {
            echo "<td class='price'>".htmlspecialchars($p1)."</td>";
            echo "<td class='price'>".htmlspecialchars($p2)."</td>";
        } elseif (!$p1 && $p2) {
            echo "<td></td><td class='price'>".htmlspecialchars($p2)."</td>";
        } elseif ($p1 && !$p2) {
            echo "<td class='price'>".htmlspecialchars($p1)."</td>";
        } else {
            echo "<td></td><td></td>";
        }
        echo "</tr>";

    endforeach; ?>

    <?php if ($thead_open) echo "</tbody>"; ?>
</table>