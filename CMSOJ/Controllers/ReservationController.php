<?php

namespace CMSOJ\Controllers;

use CMSOJ\Services\ReservationService;
use CMSOJ\Services\MailerService;

class ReservationController
{
    public function submit()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $service = new ReservationService();
            $errors  = $service->validate($_POST);

            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode(['errors' => $errors]);
                exit;
            }

            $html = $service->buildHtml($_POST);

            $mailer = new MailerService();
            $mailError = null;

            $success = $mailer->sendReservation([
                'first_name' => $_POST['first_name'] ?? '',
                'email'      => $_POST['email'] ?? '',
                'html'       => $html
            ], $mailError);

            if ($success) {
                echo json_encode([
                    'success' => '<p>We will confirm your reservation as soon as possible!</p>'
                ]);
                exit;
            }

            http_response_code(500);
            echo json_encode([
                'errors' => ['mail' => ($mailError ?: 'Unknown mail error')]
            ]);
            exit;

        } catch (\Throwable $e) {
            error_log('Reservation endpoint error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'errors' => ['server' => 'Server error. Please try again later.']
            ]);
            exit;
        }
    }
}
