<?php

namespace CMSOJ\Controllers;

use CMSOJ\Services\ReservationService;
use CMSOJ\Services\MailerService;

class ReservationController
{
    public function submit()
    {
        $service = new ReservationService();
        $errors  = $service->validate($_POST);

        if (!empty($errors)) {
            echo json_encode(['errors' => $errors]);
            return;
        }

        $html = $service->buildHtml($_POST);

        $mailer = new MailerService();
        $mailError = null;

        $success = $mailer->sendReservation([
            'first_name' => $_POST['first_name'],
            'email'      => $_POST['email'],
            'html'       => $html
        ], $mailError);

        if ($success) {
            echo json_encode([
                'success' => '<p>We will confirm your reservation as soon as possible!</p>'
            ]);
        } else {
            echo json_encode([
                'errors' => ['Mail error: ' . ($mailError ?: 'Unknown error')]
            ]);
        }
    }
}
