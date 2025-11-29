<?php

namespace CMSOJ\Legacy;

class CalendarView
{
  /**
   * Render month grid view
   */
  public function renderMonth(array $context): string
  {
    // $context contains:
    // 'year', 'month', 'events', 'unavailable', 'translated_months', etc.

    ob_start();
?>

    <!-- Month Grid HTML -->
    <div class="calendar"
      data-colors="<?= event_colors ?>"
      <?= disable_event_management ? 'data-disable-event-management="true"' : '' ?>
      <?= disable_photo_uploads ? 'data-disable-photo-uploads="true"' : '' ?>>

      <?= $this->renderHeader($context) ?>
      <?= $this->renderDays($context) ?>

    </div>

  <?php
    return ob_get_clean();
  }

  /**
   * Render header (month name, next/prev buttons)
   */
  private function renderHeader(array $ctx): string
  {
    ob_start();
  ?>

    <div class="calendar-header">
      <div class="month-year">
        <a href="#" class="current"><?= $ctx['month_name'] ?> <?= $ctx['year'] ?></a>
        <a href="#" class="today">today</a>
        <a href="#" class="refresh"><svg>…</svg></a>
        <a href="#" class="prev" data-date="<?= $ctx['prev_month'] ?>"><svg>…</svg></a>
        <a href="#" class="next" data-date="<?= $ctx['next_month'] ?>"><svg>…</svg></a>
      </div>
    </div>

  <?php
    return ob_get_clean();
  }

  /**
   * Render the day grid (names + day numbers + events)
   */
  private function renderDays(array $ctx): string
  {
    ob_start();
  ?>

    <div class="calendar-days">
      <?php foreach ($ctx['translated_days'] as $i => $day): ?>
        <div class="day_name">
          <span class="size-normal"><?= $day ?></span>
          <span class="size-mini"><?= $ctx['translated_days_mini'][$i] ?></span>
        </div>
      <?php endforeach; ?>

      <?= $this->renderDayCells($ctx) ?>
    </div>

<?php
    return ob_get_clean();
  }

  /**
   * Render the day cells including events
   */
  private function renderDayCells(array $ctx): string
  {
    ob_start();

    // Prepend blank cells
    for ($i = 0; $i < $ctx['first_day_offset']; $i++) {
      echo '<div class="day_num ignore"></div>';
    }

    // Actual day cells
    foreach ($ctx['days'] as $day) {
      $classes = "day_num";
      if ($day['is_today']) $classes .= " selected";
      if ($day['unavailable']) $classes .= " unavailable ignore";

      echo "<div class=\"$classes\" data-date=\"{$day['date']}\">";

      echo "<span class=\"day_num_value\">{$day['label']}</span>";

      if ($day['unavailable_label']) {
        echo "<span class=\"unavailable_label\">{$day['unavailable_label']}</span>";
      }

      // EVENTS RENDERING GOES HERE:
      foreach ($day['events'] as $eventHtml) {
        echo $eventHtml;
      }

      echo "</div>";
    }

    // Append trailing blank cells
    for ($i = 0; $i < $ctx['end_blanks']; $i++) {
      echo '<div class="day_num ignore"></div>';
    }

    return ob_get_clean();
  }
}
