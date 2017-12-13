<?php
namespace StudentCheckIn;
abstract class ConfCron {
    const HOURLY   = 0;
    const DALY     = 1;
    const MONTHLY  = 2;

    public static $run_all = false;
    public static $scripts = array(
        // [file_name] => self::[HOURLY | DALY | MONTHLY]
        "student_cache_update.php" => self::DALY
    );
}