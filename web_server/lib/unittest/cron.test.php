<?php
namespace StudentCheckIn;
use ccg\unittesting\ITest;
use ccg\unittesting\Assert;
use ccg\unittesting\UnitTest;

require_once '../autoload.php';
require_once './unittest.php';

require './db_setup.php';

class CronTest implements ITest {
    protected $test_name_identifier;

    public function __construct($identifier) {
        $this->test_name_identifier = $identifier;
    }
    #region ccg\unittesting\ITest Members

    /**
     * Summary of GetIdentifier
     * The tests name of this instances
     *
     * @return string
     */
    function GetIdentifier() {
        return $this->test_name_identifier;
    }

    #endregion

    public function Manage() {
        \ccg\unittesting\UnitTest::Log('Setting values up for this test...');
        $last_hourly  = RdgMetadata::Select('cron_last_run_hourly');
        $last_daly    = RdgMetadata::Select('cron_last_run_daly');
        $last_monthly = RdgMetadata::Select('cron_last_run_monthly');

        $last_hourly->value  = '0';
        $last_daly->value    = '0';
        $last_monthly->value = '0';

        RdgMetadata::Update($last_hourly);
        RdgMetadata::Update($last_daly);
        RdgMetadata::Update($last_monthly);

        \ccg\unittesting\UnitTest::Log('require: ../cron/manage.php');
        require '../cron/manage.php';
    }
}

$obj = new CronTest('Cron');
UnitTest::RegisterTest($obj);
