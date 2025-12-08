<?php
namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;
use CMSOJ\Models\Message;
use CMSOJ\Models\Event;

class DashboardController
{
    public function index()
    {
        $stats = [
            // 'messages_today' => Message::countToday(),
            // 'messages_total' => Message::countAll(),
            // 'events_today'   => Event::countToday(),
            // 'events_upcoming'=> Event::countUpcoming(),
        ];

        // return Template::view('CMSOJ/Views/admin/dashboard.html', compact('stats'));
        echo "<h1>Admin Dashboard</h1>";  
    }
}
