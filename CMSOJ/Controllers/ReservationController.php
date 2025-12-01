<?php

namespace CMSOJ\Controllers;

use CMSOJ\Services\ReservationService;

class ReservationController
{
    public function submit()
    {
        // Must be POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo '{"errors":{"general":"Method not allowed"}}';
            return;
        }

        $service = new ReservationService();
        $result  = $service->process($_POST);

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
