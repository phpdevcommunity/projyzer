<?php

namespace App\Helper;

use DateTime;

class DateHelper
{
    public static function getStartAndEndDate(int $year, int $week): array
    {
        $start = (new DateTime())->setISODate($year, $week);
        $end = (new DateTime())->setISODate($year, $week, 7);
        return [
            'start' => $start, //start date
            'end' => $end //end date
        ];
    }
}
