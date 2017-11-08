<?php
/**
 * Run this script to execute cron jobs
 */
namespace StudentCheckIn;
require_once '../autoload.php';

$last_hourly  = RdgMetadata::Select('cron_last_run_hourly');
$last_daly    = RdgMetadata::Select('cron_last_run_daly');
$last_monthly = RdgMetadata::Select('cron_last_run_monthly');

$unix_time_now = time();

#region TODO remove - it only used for unit testing
$debug_values = array($last_hourly->value, $last_daly->value, $last_monthly->value);
#endregion

$run_lvl = -1;
if (!ConfCron::$run_all) {
    if ($unix_time_now - intval($last_hourly->value) > 3599) {
        $run_lvl            = 0;
        $last_hourly->value = strval($unix_time_now);

        RdgMetadata::Update($last_hourly);
    }

    if ($unix_time_now - intval($last_daly->value) > 86399) {
        $run_lvl          = 1;
        $last_daly->value = strval($unix_time_now);

        RdgMetadata::Update($last_hourly);
    }

    $last       = explode('-', date("Y-m", intval($last_monthly->value)));
    $last_value = intval($last[0]) * 12 + intval($last[1]);

    $now       = explode('-', date("Y-m", $unix_time_now));
    $now_value = intval($now[0]) * 12 + intval($now[1]);

    if ($now_value > $last_value) {
        $run_lvl = 2;
        $last_monthly->value = strval($unix_time_now);
    }
} else {
    $run_lvl = 2;
}

#region TODO remove - it only used for unit testing
\ccg\unittesting\UnitTest::Log('The cron run lvl: ' . $run_lvl);
\ccg\unittesting\UnitTest::Log('Time now: ' . $unix_time_now);
\ccg\unittesting\UnitTest::Log('Last hourly: ' . $debug_values[0]);
\ccg\unittesting\UnitTest::Log('Last daily: ' . $debug_values[1]);
\ccg\unittesting\UnitTest::Log('Last monthly: ' . $debug_values[2]);
\ccg\unittesting\UnitTest::Log('=== Cron job starting ===');
#endregion

foreach (array_keys(ConfCron::$scripts) as $key) {
    if (ConfCron::$scripts[$key] <= $run_lvl) {
        #region TODO remove - it only used for unit testing
        \ccg\unittesting\UnitTest::Log('Executing cron job: ' . $key);
        #endregion
        require __DIR__ . '/scripts/' . $key;
    }
}
