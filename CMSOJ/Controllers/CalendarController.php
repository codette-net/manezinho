<?php

namespace CMSOJ\Controllers;

use CMSOJ\Services\CalendarService;
use CMSOJ\Views\CalendarView;
use CMSOJ\Core\Config;

class CalendarController
{
    private CalendarService $service;
    private CalendarView $view;

    public function __construct()
    {
        $this->service = new CalendarService();
        $this->view    = new CalendarView();
    }

    /**
     * Main calendar endpoint.
     * GET /calendar
     * GET /calendar?events_list=...
     * POST /calendar (add/edit event)
     */
    public function handle()
    {
        // Extract request method
        $method = $_SERVER['REQUEST_METHOD'];

        // --- POST: Add or Update event ---------------------------------------
        // if ($method === 'POST') {
        //     return $this->handleSaveEvent();
        // }

        // // --- GET: Delete event? ---------------------------------------------
        // if (isset($_GET['delete_event'])) {
        //     return $this->handleDeleteEvent((int)$_GET['delete_event']);
        // }

      // Daily events popup
        if (isset($_GET['events_list'])) {
            return $this->handleEventsList($_GET['events_list']);
        }

        $uid   = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
        $date  = $_GET['current_date'] ?? date('Y-m-d');

        $displayCalendar = isset($_GET['display_calendar'])
            ? filter_var($_GET['display_calendar'], FILTER_VALIDATE_BOOL)
            : true;

        $expandedList = isset($_GET['expanded_list'])
            ? filter_var($_GET['expanded_list'], FILTER_VALIDATE_BOOL)
            : false;

        $data = $this->service->getMonth($uid, $date, $expandedList, $displayCalendar);

        echo $this->view->render($data);
        exit;
    }


    /**
     * ---------------------------------------------------------------------
     * Handle POST for add/update event (API-compatible with legacy)
     * ---------------------------------------------------------------------
     */
    // private function handleSaveEvent()
    // {
    //     if (empty($_POST['title'])) {
    //         exit('Please enter the event title!');
    //     }

    //     if (!empty($_POST['redirect_url']) && !filter_var($_POST['redirect_url'], FILTER_VALIDATE_URL)) {
    //         exit('Please enter a valid URL!');
    //     }

    //     $uid = (int)($_POST['uid'] ?? 0);

    //     $file = (!empty($_FILES['photo']) ? $_FILES['photo'] : null);

    //     $success = $this->service->saveEvent(
    //         uid:          $uid,
    //         id:           $_POST['eventid'] ?? null,
    //         title:        $_POST['title'],
    //         description:  $_POST['description'],
    //         datestart:    $_POST['startdate'],
    //         dateend:      $_POST['enddate'],
    //         color:        $_POST['color'] ?? '',
    //         recurring:    $_POST['recurring'] ?? 'never',
    //         redirect_url: $_POST['redirect_url'] ?? '',
    //         file:         $file
    //     );

    //     echo $success ? 'success' : 'error';
    //     exit;
    // }


    /**
     * ---------------------------------------------------------------------
     * GET /calendar?delete_event=ID
     * ---------------------------------------------------------------------
     */
    // private function handleDeleteEvent(int $id)
    // {
    //     $this->service->deleteEvent($id);
    //     exit('deleted');
    // }


  

    /**
     * GET /calendar?events_list=YYYY-MM-DD
     * Used by JS when clicking a day
     */
    private function handleEventsList(string $date)
    {
        $uid     = (int)($_GET['uid'] ?? 0);
        $events  = $this->service->getEventsForDate($uid, $date);

        echo $this->view->renderDayEventsList($events, $date);
        exit;
    }
}
