<?php

namespace CMSOJ\Services;

class ReservationValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address!';
        }

        // First name
        if (!preg_match('/^[\p{L}\s\'\-]+$/u', $data['first_name'])) {
            $errors['first_name'] = 'First name must contain only characters!';
        }

        // Phone
        if (!preg_match('/^[\d\(\)\-\+]+$/', $data['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number!';
        }

        // Persons
        if ((int)$data['persons'] < 1 || (int)$data['persons'] > 20) {
            $errors['persons'] = 'Please enter a number between 1 and 20!';
        }

        // Date
        if (!strtotime($data['date']) || strtotime($data['date']) < strtotime('today')) {
            $errors['date'] = 'Please enter a valid date!';
        }

        // Time
        if (empty($data['time'])) {
            $errors['time'] = 'Please select a time!';
        }

        return $errors;
    }
}
