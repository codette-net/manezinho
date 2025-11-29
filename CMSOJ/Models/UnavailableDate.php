<?php

namespace CMSOJ\Models;

use CMSOJ\Core\Model;

class UnavailableDate extends Model
{
    protected string $table = 'unavailable_dates';

    /**
     * Get unavailable dates for a month + uid
     */
    public function getForMonth(int $uid, int $year, int $month): array
    {
        $stmt = $this->db()->prepare("
            SELECT *
            FROM unavailable_dates
            WHERE MONTH(unavailable_date) = ?
              AND YEAR(unavailable_date) = ?
              AND (event_uid = ? OR event_uid IS NULL)
        ");

        $stmt->execute([$month, $year, $uid]);
        return $stmt->fetchAll();
    }
}
