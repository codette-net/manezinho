<?php

namespace CMSOJ\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // If you need SMTP:
        // $this->mail->isSMTP();
        // $this->mail->Host       = 'smtp.example.com';
        // $this->mail->SMTPAuth   = true;
        // $this->mail->Username   = '...';
        // $this->mail->Password   = '...';
        // $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $this->mail->Port       = 587;
    }

    public function sendReservation(array $data, string $html): void
    {
        try {
            $this->mail->setFrom('info@artrestaurantmanezinho.com', $data['first_name']);
            $this->mail->addAddress('reservations@artrestaurantmanezinho.com', 'Support');

            if (!empty($data['email'])) {
                $this->mail->addReplyTo($data['email'], $data['first_name']);
            }

            $this->mail->isHTML(true);
            $this->mail->Subject = 'RESERVATION for ' . $data['first_name'] . ' - ' . $data['email'];
            $this->mail->Body    = $html;
            $this->mail->AltBody = strip_tags($html);

            $this->mail->send();
        } catch (Exception $e) {
            throw new \Exception("Mailer Error: " . $this->mail->ErrorInfo);
        }
    }
}
