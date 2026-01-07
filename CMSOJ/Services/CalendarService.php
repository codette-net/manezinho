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

  $rangeStart = new DateTime(sprintf('%04d-%02d-01 00:00:00', $year, $month));
  $rangeEnd   = (clone $rangeStart)->modify('last day of this month')->setTime(23, 59, 59);

  foreach ($events as $event) {
    $rec = (string)($event['recurring'] ?? 'never');
    if ($rec === 'never') {
      continue;
    }

    $result = array_merge($result, $this->expandRecurringInRange($event, $rangeStart, $rangeEnd));
  }

  return $result;
}

/**
 * Expand a recurring event into occurrences that overlap the requested month range.
 * This fixes:
 *  - repeats not showing in the same month
 *  - repeats stopping after a fixed number of iterations
 */
private function expandRecurringInRange(array $event, DateTime $rangeStart, DateTime $rangeEnd): array
{
  $rec = (string)($event['recurring'] ?? 'never');
  if ($rec === 'never') return [];

  $start0 = new DateTime($event['datestart']);
  $end0   = new DateTime($event['dateend']);

  // duration in seconds (keep same duration on each occurrence)
  $duration = max(0, $end0->getTimestamp() - $start0->getTimestamp());

  // Determine step
  $stepSpec = match ($rec) {
    'daily'   => '+1 day',
    'weekly'  => '+1 week',
    'monthly' => '+1 month',
    'yearly'  => '+1 year',
    default   => null,
  };

  if ($stepSpec === null) return [];

  // Move cursor to the first occurrence that could overlap the range.
  // We do that by stepping forward until end >= rangeStart.
  $cursorStart = clone $start0;
  $cursorEnd   = (clone $cursorStart);
  $cursorEnd->modify('+' . $duration . ' seconds');

  // Safety cap to prevent infinite loop if bad data (should never hit)
  $guard = 0;

  while ($cursorEnd < $rangeStart) {
    $cursorStart->modify($stepSpec);
    $cursorEnd = (clone $cursorStart)->modify('+' . $duration . ' seconds');

    if (++$guard > 5000) {
      break;
    }
  }

  $out = [];

  // Now generate occurrences until start > rangeEnd
  while ($cursorStart <= $rangeEnd) {
    // include if overlaps [rangeStart, rangeEnd]
    if ($cursorEnd >= $rangeStart) {
      $clone = $event;
      $clone['datestart'] = $cursorStart->format('Y-m-d H:i:s');
      $clone['dateend']   = $cursorEnd->format('Y-m-d H:i:s');
      $out[] = $clone;
    }

    $cursorStart->modify($stepSpec);
    $cursorEnd = (clone $cursorStart)->modify('+' . $duration . ' seconds');

    if (++$guard > 5000) {
      break;
    }
  }

  return $out;
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
