<?php
// Bootstrap config + DB + legacy calendar class

require_once __DIR__ . '/../CMSOJ/Core/Config.php';
CMSOJ\Core\Config::load();

require_once __DIR__ . '/../CMSOJ/Core/Database.php';
require_once __DIR__ . '/../CMSOJ/Legacy/Calendar.php';

// Get the current date (if specified); default is null
$current_date = isset($_GET['current_date']) && strtotime($_GET['current_date']) ? $_GET['current_date'] : null;

// Get the unique id (if specified); default is 0
$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;

// Get display calendar parameter (if specified); default is false
$display_calendar = isset($_GET['display_calendar']) ? (bool)$_GET['display_calendar'] : false;

// Get expanded list parameter (if specified); default is false
$expanded_list = isset($_GET['expanded_list']) ? (bool)$_GET['expanded_list'] : false;

// Create a new calendar instance
$calendar = new Calendar($current_date, $uid, $expanded_list, $display_calendar);

// Use the NEW PDO connection from your framework
$pdo = CMSOJ\Core\Database::connect();
$calendar->set_database_connection($pdo);

// IMPORTANT: in original connect_to_database() it also did update_unavailable_dates()
$calendar->update_unavailable_dates();

// Check if the event management is disabled
if (!disable_event_management) {
    // Check if the add/update event form was submitted 
    if (isset($_POST['title'], $_POST['description'], $_POST['startdate'], $_POST['enddate'], $_POST['color'], $_POST['recurring'], $_POST['redirect_url'])) {
        // Title cannot be empty
        if (empty($_POST['title'])) {
            exit('Please enter the event title!');
        }
        // Validate url if not empty
        if (!empty($_POST['redirect_url']) && !filter_var($_POST['redirect_url'], FILTER_VALIDATE_URL)) {
            exit('Please enter a valid URL!');
        }
        // Check if date is unavailable
        if ($calendar->is_date_unavailable($_POST['startdate'], $_POST['enddate'])) {
            exit('The selected date is unavailable!');
        }
        // Validate the color
        $color = in_array($_POST['color'], explode(',', event_colors)) ? $_POST['color'] : '#5373ae';
        // Photo upload
        $photo = !disable_photo_uploads && isset($_FILES['photo']) ? $_FILES['photo'] : null;
        // If the event ID exists, update the corresponding event otherwise add a new event 
        if (empty($_POST['eventid'])) {
            // Add new event
            $calendar->add_event($_POST['title'], $_POST['description'], $_POST['startdate'], $_POST['enddate'], $color, $_POST['recurring'], $photo, $_POST['redirect_url']);
        } else {
            // Update existing event
            $calendar->update_event($_POST['eventid'], $_POST['title'], $_POST['description'], $_POST['startdate'], $_POST['enddate'], $color, $_POST['recurring'], $photo, $_POST['redirect_url']);
        }
        exit('success');
    }
    // Delete event
    if (isset($_GET['delete_event'])) {
        $calendar->delete_event($_GET['delete_event']);
        exit;
    }
}

// Retrieve events list in HTML format
if (isset($_GET['events_list'])) {
    $events_list = $calendar->list_events_by_date_html($_GET['events_list']);
    if ($events_list) {
        echo $events_list;
    } else {
        echo '<div class="events"><span class="no-events">There are no events.</span></div>';
    }
    exit;
}

// Display the calendar (HTML from __toString())
echo $calendar;
