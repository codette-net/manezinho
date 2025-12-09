<?php
require 'main.php'; // includes $pdo connection

libxml_use_internal_errors(true);

// Load HTML and normalize encoding
// $html = file_get_contents('menuEnglish.html');
$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

$dom = new DOMDocument('1.0', 'UTF-8');
$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
$xpath = new DOMXPath($dom);

// --- helper functions ---
function clean_text($text) {
    $text = trim(preg_replace('/\s+/', ' ', $text));
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace(['â‚¬', 'EUR'], '€', $text);
    return $text !== '' ? $text : null;
}
function is_numeric_price($text) {
    return preg_match('/\d/', $text);
}

// --- main scraper ---
$sections = $xpath->query('//section');

foreach ($sections as $section) {
    $section_id = $section->getAttribute('id');
    $section_name = ucfirst($section_id ?: 'Untitled Section');

    // Insert main section
    $stmt = $pdo->prepare("INSERT INTO menu_sections (name_en, is_active) VALUES (?, 1)");
    $stmt->execute([$section_name]);
    $main_section_id = $pdo->lastInsertId();

    echo "Imported section: $section_name\n";

    // Each <table> = one subsection
    $tables = $xpath->query('.//table', $section);
    foreach ($tables as $table) {
        // Subsection title from <thead><th>
        $thead_th = $xpath->query('.//thead//th', $table)->item(0);
        $subsection_name = $thead_th ? clean_text($thead_th->textContent) : null;

        $stmt = $pdo->prepare("INSERT INTO menu_sections (parent_id, name_en, is_active) VALUES (?, ?, 1)");
        $stmt->execute([$main_section_id, $subsection_name]);
        $subsection_id = $pdo->lastInsertId();

        echo "  Subsection: " . ($subsection_name ?: '[Untitled]') . "\n";

        // --- handle rows ---
        $rows = $xpath->query('.//tbody/tr', $table);
        foreach ($rows as $row) {
            $cells = [];
            $tds = $row->getElementsByTagName('td');
            $ths = $row->getElementsByTagName('th');

            // collect all visible text (supports td or th rows)
            if ($tds->length > 0) {
                foreach ($tds as $td) $cells[] = clean_text($td->textContent);
            } elseif ($ths->length > 0) {
                foreach ($ths as $th) $cells[] = clean_text($th->textContent);
            } else continue;

            $cell_count = count($cells);

            // description inside first <td>
            $nameNode = $tds->length > 0 ? $tds->item(0) : ($ths->length > 0 ? $ths->item(0) : null);
            $descNode = $nameNode ? $xpath->query('.//small/em', $nameNode)->item(0) : null;
            $description = $descNode ? clean_text($descNode->textContent) : null;

            // defaults
            $name = $unit_1_label = $unit_2_label = null;
            $price_1 = $price_2 = null;

            // interpret row type
            if ($cell_count === 1) {
                if (is_numeric_price($cells[0])) $price_1 = $cells[0];
                else $name = $cells[0];
            } elseif ($cell_count === 2) {
                $first = $cells[0] ?? null;
                $second = $cells[1] ?? null;

                // Case: empty first td and second has price → price_2
                if (!$first && is_numeric_price($second)) {
                    $price_2 = $second;
                }
                // Normal case: name + price
                elseif ($first && is_numeric_price($second)) {
                    $name = $first;
                    $price_1 = $second;
                }
                // Both numeric (two prices)
                elseif (is_numeric_price($first) && is_numeric_price($second)) {
                    $price_1 = $first;
                    $price_2 = $second;
                }
                // Both text (units)
                elseif (!is_numeric_price($first) && !is_numeric_price($second)) {
                    $unit_1_label = $first;
                    $unit_2_label = $second;
                }
                // Only first has value
                else {
                    $name = $first ?: $second;
                }
            } elseif ($cell_count >= 3) {
                $first = $cells[0] ?? null;
                $second = $cells[1] ?? null;
                $third = $cells[2] ?? null;

                // Name
                if ($first && !is_numeric_price($first)) $name = $first;
                // Prices or labels
                if ($second && is_numeric_price($second)) $price_1 = $second;
                elseif ($second) $unit_1_label = $second;

                if ($third && is_numeric_price($third)) $price_2 = $third;
                elseif ($third) $unit_2_label = $third;
            }

            // skip empty rows
            if (!$name && !$unit_1_label && !$price_1 && !$price_2) continue;

            // Insert item
            $stmt = $pdo->prepare("
                INSERT INTO menu_items (
                    section_id, name_en, description_en,
                    unit_1_label, price_1, unit_2_label, price_2, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $subsection_id,
                $name,
                $description,
                $unit_1_label,
                $price_1,
                $unit_2_label,
                $price_2
            ]);
        }
    }
}

echo "\n✅ Import complete — correct single-price logic, UTF-8 fixed, <thead>/<tbody> handling, and descriptions.\n";
