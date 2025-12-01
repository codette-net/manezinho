<?php

namespace CMSOJ\Services;

class ReservationService
{
    private ReservationValidator $validator;
    private MailService $mail;

    public function __construct()
    {
        $this->validator = new ReservationValidator();
        $this->mail      = new MailService();
    }

    public function process(array $data): array
    {
        $errors = $this->validator->validate($data);

        if ($errors) {
            return ['errors' => $errors];
        }

        // Format date
        $dateFormatted = date('l j F Y', strtotime($data['date']));

        // Build email HTML
        $html =
            'Name: ' . $data['first_name'] . '<br>' .
            'Email: ' . $data['email'] . '<br>' .
            'Phone: ' . $data['phone'] . '<br>' .
            'Number of persons: ' . $data['persons'] . '<br>' .
            'Date: ' . $dateFormatted . '<br>' .
            'Time: ' . $data['time'] . '<br>' .
            (!empty($data['message']) ? 'Message: ' . $data['message'] . '<br>' : '');

        // Send mail
        $this->mail->sendReservation($data, $html);

        return ['success' => '<p>We will confirm your reservation as soon as possible!</p>'];
    }
}
