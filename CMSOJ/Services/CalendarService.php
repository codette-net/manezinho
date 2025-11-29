<?php

namespace CMSOJ\Services;

use CMSOJ\Models\Event;
use CMSOJ\Models\UnavailableDate;
use DateTime;

class CalendarService
{
  private Event $eventModel;
  private UnavailableDate $unavailableModel;

  public function __construct()
  {
    $this->eventModel     = new Event();
    $this->unavailableModel = new UnavailableDate();
  }

  /**
   * Main API for controller: month view.
   */
  public function getMonth(
    int $uid,
    string $date,
    bool $expandedList    = false,
    bool $displayCalendar = true
  ): array {
    // Normalize date -> year/month
    [$year, $month] = explode('-', date('Y-m', strtotime($date)));
    $year  = (int) $year;
    $month = (int) $month;

    $meta        = $this->getMonthMeta($year, $month);
    $unavailable = $this->unavailableModel->getForMonth($uid, $year, $month);
    $events      = $this->getEvents($uid, $year, $month);

    // Build day skeletons
    $days = $this->buildDays($year, $month, $unavailable);

    // Attach events to those days
    $days = $this->attachEventsToDays($days, $events);

    $offset    = $this->getFirstDayOffset($year, $month);
    $endBlanks = $this->getEndBlanks($year, $month, $offset);

    return array_merge($meta, [
      'days'             => $days,
      'offset'           => $offset,
      'end_blanks'       => $endBlanks,
      'unavailable'      => $unavailable,
      'events'           => $events,
      'expanded_list'    => $expandedList,
      'display_calendar' => $displayCalendar,
    ]);
  }

  /**
   * Used by /calendar?events_list=YYYY-MM-DD
   */
  public function getEventsForDate(int $uid, string $date): array
  {
    [$year, $month] = explode('-', date('Y-m', strtotime($date)));
    $year  = (int) $year;
    $month = (int) $month;

    $allEvents = $this->getEvents($uid, $year, $month);

    $target = (new DateTime($date))->format('Y-m-d');

    $result = [];
    foreach ($allEvents as $event) {
      $start = new DateTime($event['datestart']);
      $end   = new DateTime($event['dateend']);

      // Normalize to date only
      $startDate = $start->format('Y-m-d');
      $endDate   = $end->format('Y-m-d');

      if ($target >= $startDate && $target <= $endDate) {
        $result[] = $event;
      }
    }

    // Sort by start time
    usort($result, fn($a, $b) => strtotime($a['datestart']) <=> strtotime($b['datestart']));

    return $result;
  }

  /**
   * ---------------- META + LAYOUT HELPERS --------------
   */
  public function getMonthMeta(int $year, int $month): array
  {
    $translatedMonths = [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December'
    ];

    $translatedDays      = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $translatedDaysShort = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];

    return [
      'year'                 => $year,
      'month'                => $month,
      'month_name'           => $translatedMonths[$month - 1],
      'translated_days'      => $translatedDays,
      'translated_days_mini' => $translatedDaysShort,
      'prev_month'           => date('Y-m-01', strtotime("$year-$month-01 -1 month")),
      'next_month'           => date('Y-m-01', strtotime("$year-$month-01 +1 month")),
    ];
  }

  private function buildDays(int $year, int $month, array $unavailable): array
  {
    $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    $unavailableIndex = [];
    foreach ($unavailable as $row) {
      $unavailableIndex[$row['unavailable_date']] = $row['unavailable_label'] ?? '';
    }

    $days = [];
    for ($i = 1; $i <= $totalDays; $i++) {
      $date = sprintf('%04d-%02d-%02d', $year, $month, $i);

      $isToday = ($date === date('Y-m-d'));

      $unavailableFlag  = array_key_exists($date, $unavailableIndex);
      $unavailableLabel = $unavailableFlag ? $unavailableIndex[$date] : '';

      $days[] = [
        'label'             => $i,
        'label_full'        => $i . ' ' . date('F', strtotime("$year-$month-01")) . ' ' . $year,
        'date'              => $date,
        'is_today'          => $isToday,
        'unavailable'       => $unavailableFlag,
        'unavailable_label' => $unavailableLabel,
        'events'            => [],
      ];
    }

    return $days;
  }

  public function getFirstDayOffset(int $year, int $month): int
  {
    // Monday-based calendar like legacy
    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    $firstDayText = date('D', strtotime("$year-$month-01"));
    $offset       = array_search($firstDayText, $days);

    return $offset === false ? 0 : $offset;
  }

  public function getEndBlanks(int $year, int $month, int $offset): int
  {
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $used        = $daysInMonth + $offset;
    $blanks      = 42 - $used; // 6 rows * 7 columns

    return max(0, $blanks);
  }

  /**
   * ---------------- EVENTS + RECURRING --------------
   */
  public function getEvents(int $uid, int $year, int $month): array
  {
    $events = $this->eventModel->getEventsForMonth($uid, $year, $month);

    // Expand recurring
    $events = $this->addRecurringEvents($events, $year, $month);

    // Remove duplicates
    return $this->uniqueEvents($events);
  }

  private function addRecurringEvents(array $events, int $year, int $month): array
  {
    $result = $events;

    foreach ($events as $event) {
      switch ($event['recurring']) {
        case 'daily':
          $result = array_merge($result, $this->expandDaily($event, $year, $month));
          break;
        case 'weekly':
          $result = array_merge($result, $this->expandWeekly($event, $year, $month));
          break;
        case 'monthly':
          $result = array_merge($result, $this->expandMonthly($event, $year, $month));
          break;
        case 'yearly':
          $result = array_merge($result, $this->expandYearly($event, $year, $month));
          break;
      }
    }

    return $result;
  }

  private function uniqueEvents(array $events): array
  {
    $seen = [];
    $out  = [];

    foreach ($events as $event) {
      $id = $event['id'] ?? null;

      $keyParts = [
        $id ?: ($event['title'] ?? ''),
        $event['datestart'] ?? '',
        $event['dateend'] ?? '',
      ];
      $key = implode('|', $keyParts);

      if (isset($seen[$key])) {
        continue;
      }

      $seen[$key] = true;
      $out[]      = $event;
    }

    return $out;
  }

  private function expandDaily(array $event, int $year, int $month): array
  {
    $out   = [];
    $start = new DateTime($event['datestart']);
    $end   = new DateTime($event['dateend']);

    $origMonth   = date('Y-m', strtotime($event['datestart']));
    $targetMonth = sprintf('%04d-%02d', $year, $month);

    for ($i = 1; $i <= 31; $i++) {
      $startClone = clone $start;
      $endClone   = clone $end;

      $startClone->modify("+$i day");
      $endClone->modify("+$i day");

      $cloneMonth = $startClone->format('Y-m');

      // skip original month
      if ($cloneMonth === $origMonth) {
        continue;
      }

      if ($cloneMonth === $targetMonth || $endClone->format('Y-m') === $targetMonth) {
        $clone              = $event;
        $clone['datestart'] = $startClone->format('Y-m-d H:i:s');
        $clone['dateend']   = $endClone->format('Y-m-d H:i:s');
        $out[]              = $clone;
      }
    }

    return $out;
  }

  private function expandWeekly(array $event, int $year, int $month): array
  {
    $out   = [];
    $start = new DateTime($event['datestart']);
    $end   = new DateTime($event['dateend']);

    $origMonth   = date('Y-m', strtotime($event['datestart']));
    $targetMonth = sprintf('%04d-%02d', $year, $month);

    for ($i = -4; $i <= 8; $i++) {
      $startClone = clone $start;
      $endClone   = clone $end;

      $startClone->modify("+$i week");
      $endClone->modify("+$i week");

      $cloneMonth = $startClone->format('Y-m');

      // skip same month
      if ($cloneMonth === $origMonth) continue;

      if ($cloneMonth === $targetMonth || $endClone->format('Y-m') === $targetMonth) {
        $clone              = $event;
        $clone['datestart'] = $startClone->format('Y-m-d H:i:s');
        $clone['dateend']   = $endClone->format('Y-m-d H:i:s');
        $out[]              = $clone;
      }
    }

    return $out;
  }

  private function expandMonthly(array $event, int $year, int $month): array
  {
    $out         = [];
    $origMonth   = date('Y-m', strtotime($event['datestart']));
    $targetMonth = sprintf('%04d-%02d', $year, $month);

    $start = new DateTime($event['datestart']);
    $end   = new DateTime($event['dateend']);

    for ($i = -1; $i <= 1; $i++) {
      $startClone = clone $start;
      $endClone   = clone $end;

      $startClone->modify("+$i month");
      $endClone->modify("+$i month");

      $cloneMonth = $startClone->format('Y-m');

      if ($cloneMonth === $origMonth) continue;

      if ($cloneMonth === $targetMonth || $endClone->format('Y-m') === $targetMonth) {
        $clone              = $event;
        $clone['datestart'] = $startClone->format('Y-m-d H:i:s');
        $clone['dateend']   = $endClone->format('Y-m-d H:i:s');
        $out[]              = $clone;
      }
    }

    return $out;
  }

  private function expandYearly(array $event, int $year, int $month): array
  {
    $out = [];

    $origMonth = date('m', strtotime($event['datestart']));

    $start = new DateTime($event['datestart']);
    $end   = new DateTime($event['dateend']);

    // Apply the new year
    $start->setDate($year, (int)$start->format('m'), (int)$start->format('d'));
    $end->setDate($year, (int)$end->format('m'), (int)$end->format('d'));

    $cloneMonth = $start->format('m');

    // skip same month = original occurrence
    if ($cloneMonth == $origMonth) {
      return [];
    }

    if ((int)$cloneMonth === $month || (int)$end->format('m') === $month) {
      $clone              = $event;
      $clone['datestart'] = $start->format('Y-m-d H:i:s');
      $clone['dateend']   = $end->format('Y-m-d H:i:s');
      $out[]              = $clone;
    }

    return $out;
  }

  /**
   * Attach events to day structures.
   * Simple version: no fancy "pos" stacking, just grouped and sorted.
   */
  public function attachEventsToDays(array $days, array $events): array
  {
    foreach ($events as $event) {
      $start = new DateTime($event['datestart']);
      $end   = new DateTime($event['dateend']);

      foreach ($days as &$day) {
        $dayDate = new DateTime($day['date']);

        if (
          $dayDate >= (clone $start)->setTime(0, 0, 0) &&
          $dayDate <= (clone $end)->setTime(0, 0, 0)
        ) {
          // Determine status: start / ongoing / end
          $status    = 'single';
          $startDate = (new DateTime($event['datestart']))->format('Y-m-d');
          $endDate   = (new DateTime($event['dateend']))->format('Y-m-d');
          $current   = $dayDate->format('Y-m-d');

          if ($startDate === $endDate) {
            $status = 'single';
          } elseif ($current === $startDate) {
            $status = 'start';
          } elseif ($current === $endDate) {
            $status = 'end';
          } else {
            $status = 'ongoing';
          }

          $eventCopy           = $event;
          $eventCopy['status'] = $status;

          $day['events'][] = $eventCopy;
        }
      }
    }

    // Sort events in each day by start time
    foreach ($days as &$day) {
      usort($day['events'], fn($a, $b) => strtotime($a['datestart']) <=> strtotime($b['datestart']));
    }

    return $days;
  }
}
