<?php

namespace CMSOJ\Views;
use CMSOJ\Core\Config;

class CalendarView
{
    /**
     * Render full calendar (expanded view + grid).
     */
    public function render(array $data): string
    {
        $html = '';
        // Optional expanded month list at the top
        if (!empty($data['expanded_list'])) {
            $html .= $this->renderExpandedMonth($data['events'], $data);
        }
        $html .= '<div class="calendar">';


        // Main calendar grid
        if (!empty($data['display_calendar'])) {
            $html .= $this->renderHeader($data);
            $html .= $this->renderGrid($data);
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * ---------------- HEADER ----------------
     * .calendar-header .prev/.next/.today/.refresh/.current
     */
    private function renderHeader(array $data): string
    {
        
        return '
            <div class="calendar-header">
                <div class="month-year">
                    <a href="#" class="current" title="Select Date"
                    data-date="' . $data['year'] . '-' . sprintf('%02d', $data['month']) . '-01">
                        ' . $data['month_name'] . ' ' . $data['year'] . '
                    </a>

                    <a href="#" class="today">today</a>
                    <a href="#" class="refresh" title="Refresh">
                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z" /></svg>
                    </a>

                    <a href="#" class="prev"
                    title="Previous Month"
                    data-date="' . $data['prev_month'] . '">
                    <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" /></svg></a>

                    <a href="#" class="next"
                    title="Next Month"
                    data-date="' . $data['next_month'] . '">
                    <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" /></svg>
                    </a>
                </div>
            </div>
        ';
    }

    /**
     * ---------------- GRID WRAPPER ----------------
     * <div class="calendar" data-colors="...">
     */
    private function renderGrid(array $data): string
    {
        $attrs = [];

        // data-colors from legacy constant
        if (defined('event_colors')) {
            $attrs[] = 'data-colors="' . htmlspecialchars(event_colors, ENT_QUOTES) . '"';
        }

        if (Config::get('DISABLE_EVENT_MANAGEMENT')) {
            $attrs[] = 'data-disable-event-management="true"';
        }

        if (Config::get('DISABLE_PHOTO_UPLOADS')) {
            $attrs[] = 'data-disable-photo-uploads="true"';
        }

        $html = '<div class="calendar" ' . implode(' ', $attrs) . '>';
        $html .= '<div class="calendar-days">';

        // DAY NAMES
        foreach ($data['translated_days'] as $i => $label) {
            $html .= '
                <div class="day_name">
                    <span class="size-normal">' . $label . '</span>
                    <span class="size-mini">' . $data['translated_days_mini'][$i] . '</span>
                </div>';
        }

        // LEADING BLANKS
        for ($i = 0; $i < $data['offset']; $i++) {
            $html .= '<div class="day_num ignore"></div>';
        }       

        // DAYS
        foreach ($data['days'] as $day) {
            $html .= $this->renderDayCell($day, $data);
        }

        // TRAILING BLANKS
        for ($i = 0; $i < $data['end_blanks']; $i++) {
            $html .= '<div class="day_num ignore" style="z-index:1"></div>';
        }

        $html .= '</div>'; // .calendar-days
        $html .= '</div>'; // .calendar

        return $html;
    }

    /**
     * ---------------- SINGLE DAY CELL ----------------
     * .calendar-days .day_num:not(.ignore)
     */
    private function renderDayCell(array $day, array $data): string
    {
        $classes = ['day_num'];

        if (!empty($day['is_today'])) {
            $classes[] = 'selected';
        }

        if (!empty($day['unavailable'])) {
            $classes[] = 'unavailable';
            $classes[] = 'ignore';
        }

        $html = '<div class="' . implode(' ', $classes) . '" ' .
            'data-date="' . $day['date'] . '" ' .
            'data-label="' . htmlspecialchars($day['label_full'], ENT_QUOTES) . '">';

        // Day number
        $html .= '<span class="day_num_value">' . $day['label'] . '</span>';

        // Optional unavailable label
        if (!empty($day['unavailable_label'])) {
            $html .= '<span class="unavailable_label">' .
                htmlspecialchars($day['unavailable_label'], ENT_QUOTES) .
                '</span>';
        }

        // Events inside this day
        if (!empty($day['events'])) {
            $pos = 0;
            foreach ($day['events'] as $event) {
                $html .= $this->renderEvent($event, $day['date'], $pos++);
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * ---------------- EVENT BUBBLE ----------------
     * .event, .event-start, .event-ongoing, .event-end
     */
    private function renderEvent(array $event, string $currentDay, int $pos): string
    {
        $classes = ['event'];

        $status = $event['status'] ?? 'single';
        if ($status === 'start') {
            $classes[] = 'event-start';
        } elseif ($status === 'ongoing') {
            $classes[] = 'event-ongoing';
        } elseif ($status === 'end') {
            $classes[] = 'event-end';
        }

        $styleParts = [];
        // order like legacy "pos"
        $styleParts[] = 'order:' . $pos;

        if (!empty($event['color'])) {
            $styleParts[] = 'background-color:' . htmlspecialchars($event['color'], ENT_QUOTES);
        }

        $style = $styleParts ? ' style="' . implode(';', $styleParts) . '"' : '';

        $html = '<div class="' . implode(' ', $classes) . '"' . $style . '>';

        // Show title on start/day OR for single-day events
        if ($status === 'start' || $status === 'single') {
            $title = htmlspecialchars($event['title'] ?? '', ENT_QUOTES);
            if (!empty($title)) {
                $html .= '<span class="size-normal">' . $title . '</span>';
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * ---------------- EXPANDED MONTH VIEW ----------------
     * This corresponds to old expanded_view()
     */
    private function renderExpandedMonth(array $events, array $data): string
    {
        $currentMonthLabel      = $data['month_name'];
        $currentMonthShortLabel = substr($data['month_name'], 0, 3);
        $year                   = $data['year'];
        $month                  = $data['month'];

        // Filter to events that touch this month
        $filtered = [];
        foreach ($events as $event) {
            $eventMonth = date('Y-m', strtotime($event['datestart']));
            $current    = sprintf('%04d-%02d', $year, $month);

            if ($eventMonth === $current) {
                $filtered[] = $event;
            }
        }

        if (!$filtered) {
            return '';
        }

        // Sort by datestart
        usort($filtered, fn($a, $b) => strtotime($a['datestart']) <=> strtotime($b['datestart']));

        $html = '<div class="calendar-expanded-view normal">';
        $html .= '<h3 class="heading">Events for ' . $currentMonthLabel . '</h3>';

        foreach ($filtered as $event) {
            $html .= $this->renderExpandedEvent($event, $currentMonthLabel, $currentMonthShortLabel);
        }

        $html .= '</div>';

        return $html;
    }

    private function renderExpandedEvent(array $event, string $currentMonthLabel, string $currentMonthShortLabel): string
    {
        $start = strtotime($event['datestart']);
        $end   = strtotime($event['dateend']);

        $day  = date('d', $start);
        $timeFrom = date('G:ia', $start);
        $timeTo   = date('G:ia', $end);
        $daySuffix = date('jS', $end);

        $color = htmlspecialchars($event['color'] ?? '#5373ae', ENT_QUOTES);

        $html = '<div class="event">';
        $html .= '<div class="date">';
        $html .= '<div class="day" style="border-right:4px solid ' . $color . '">' . $day . '</div>';
        $html .= '<div class="month" style="border-right:4px solid ' . $color . '">' . strtoupper($currentMonthShortLabel) . '</div>';
        $html .= '</div>';

        if (!empty($event['photo_url'])) {
            $html .= '<div class="photo"><img src="' . htmlspecialchars($event['photo_url'], ENT_QUOTES) .
                '" width="100" height="100" alt="' . htmlspecialchars($event['title'] ?? '', ENT_QUOTES) . '"></div>';
        }

        $html .= '<div class="con">';

        $title = htmlspecialchars($event['title'] ?? '', ENT_QUOTES);

        if (!empty($event['redirect_url'])) {
            $href  = htmlspecialchars($event['redirect_url'], ENT_QUOTES);
            $html .= '<h3 class="title"><a href="' . $href . '" rel="noopener noreferrer nofollow">' .
                $title . '</a></h3>';
        } else {
            $html .= '<h3 class="title">' . $title . '</h3>';
        }

        if (!empty($event['description'])) {
            $html .= '<p class="description">' .
                nl2br(htmlspecialchars($event['description'], ENT_QUOTES)) .
                '</p>';
        }

        $html .= '<span class="time">From ' . $timeFrom . ' to ' . $timeTo .
            ' on ' . $daySuffix . ' ' . $currentMonthLabel . '</span>';

        $html .= '</div>'; // .con
        $html .= '</div>'; // .event

        return $html;
    }

    /**
     * --------------- DAILY EVENTS LIST (modal) ----------------
     * Used by /calendar?events_list=YYYY-MM-DD
     * This is the replacement of legacy list_events_by_date_html()
     */
    public function renderDayEventsList(array $events, string $date): string
    {
        if (!$events) {
            return '<div class="events"><span class="no-events">There are no events.</span></div>';
        }

        $html = '<div class="events">';

        $displayDate = date('j F Y', strtotime($date));

        foreach ($events as $event) {
            $html .= $this->renderDayEventRow($event, $displayDate);
        }

        $html .= '</div>';

        return $html;
    }

    private function renderDayEventRow(array $event, string $displayDate): string
    {
        $eventDate = date('Y-m-d', strtotime($displayDate));
        $start     = strtotime($event['datestart']);
        $end       = strtotime($event['dateend']);

        $startDate = date('Y-m-d', $start);
        $timeLabel = ($eventDate === $startDate)
            ? date('H:ia', $start)
            : 'Ongoing';

        $color = htmlspecialchars($event['color'] ?? '#5373ae', ENT_QUOTES);

        $html = '<div class="event" ' .
            'data-id="' . (int)($event['id'] ?? 0) . '" ' .
            'data-title="' . htmlspecialchars($event['title'] ?? '', ENT_QUOTES) . '" ' .
            'data-start="' . htmlspecialchars($event['datestart'], ENT_QUOTES) . '" ' .
            'data-end="' . htmlspecialchars($event['dateend'], ENT_QUOTES) . '" ' .
            'data-color="' . $color . '" ' .
            'data-date="' . htmlspecialchars($displayDate, ENT_QUOTES) . '" ' .
            'data-recurring="' . htmlspecialchars($event['recurring'] ?? 'never', ENT_QUOTES) . '" ' .
            'data-photo-url="' . htmlspecialchars($event['photo_url'] ?? '', ENT_QUOTES) . '" ' .
            'data-redirect-url="' . htmlspecialchars($event['redirect_url'] ?? '', ENT_QUOTES) . '">';

        $html .= '
            <div class="details">
                <i class="date" style="border-right:3px solid ' . $color . '">' . $timeLabel . '</i>
                <div class="title">';

        if (!empty($event['redirect_url'])) {
            $href  = htmlspecialchars($event['redirect_url'], ENT_QUOTES);
            $html .= '<a href="' . $href . '" rel="noopener noreferrer nofollow">' .
                htmlspecialchars($event['title'] ?? '', ENT_QUOTES) .
                '</a>';
        } else {
            $html .= htmlspecialchars($event['title'] ?? '', ENT_QUOTES);
        }

        $html .= '</div>
                <div class="actions">';

        if (!Config::get('DISABLE_EVENT_MANAGEMENT')) {
            $html .= '
                <svg class="edit" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>Edit Event</title><path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" /></svg>
                <svg class="delete" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>Delete Event</title><path d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M7,6H17V19H7V6M9,8V17H11V8H9M13,8V17H15V8H13Z" /></svg>';
        }

        $html .= '
                </div>
            </div>
        ';

        if (!empty($event['description'])) {
            $html .= '<p class="description">' .
                nl2br(htmlspecialchars($event['description'], ENT_QUOTES)) .
                '</p>';
        }

        $html .= '</div>';

        return $html;
    }
}
