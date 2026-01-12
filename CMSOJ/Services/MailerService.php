<?php

namespace CMSOJ\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use CMSOJ\Core\Config;

class MailerService
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // DEV DEBUG: log SMTP conversation to PHP error_log
        if (strtolower((string)Config::get('APP_ENV')) !== 'production') {
            $this->mail->SMTPDebug  = 2;
            $this->mail->Debugoutput = 'error_log';
        } else {
            $this->mail->SMTPDebug = 0;
        }


        // If SMTP mode enabled use it
        // If SMTP mode enabled use it
        if (strtolower((string)Config::get('SMTP')) === 'true') {
            $this->mail->isSMTP();
            $this->mail->Host = (string)Config::get('SMTP_HOST');

            $port = (int)Config::get('SMTP_PORT');
            $this->mail->Port = $port;

            $this->mail->SMTPAuth = true;
            $this->mail->Username = (string)Config::get('SMTP_USER');
            $this->mail->Password = (string)Config::get('SMTP_PASS');

            // Encryption based on port
            if ($port === 465) {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // implicit TLS
            } else {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS (usually 587)
            }

            // Optional but often helps on shared hosting:
            $this->mail->SMTPAutoTLS = true;
        }


        $this->mail->isHTML(true);
    }

    /**
     * Returns true on success, false on failure
     * and puts a readable error message in $error (by reference).
     */
    public function sendReservation(array $data, ?string &$error = null): bool
    {
        try {
            $this->mail->setFrom((string)Config::get('SMTP_USER'), 'Reservations');
            $this->mail->addAddress((string)Config::get('MAIL_FROM'), 'Reservations');


            if (!empty($data['email'])) {
                $this->mail->addReplyTo($data['email'], $data['first_name'] ?? '');
            }

            $this->mail->Subject = 'RESERVATION for ' . $data['first_name'] . ' - ' . $data['email'];
            $this->mail->Body    = $data['html'];
            $this->mail->AltBody = strip_tags($data['html']);

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            // Try ErrorInfo first, fall back to exception message
            $error = $this->mail->ErrorInfo ?: $e->getMessage();
            error_log('Reservation mail error: ' . $error);
            return false;
        }
    }
}
