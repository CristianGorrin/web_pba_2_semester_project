<?php
namespace StudentCheckIn;
use ccg\unittesting\ITest;
use ccg\unittesting\Assert;
use ccg\unittesting\UnitTest;

require_once '../autoload.php';
require_once './unittest.php';

class UtilTest implements ITest {
    const PATTERN_UUID_V4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

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

    #region UUID
    public function UUID_CreateV4() {
        for ($i = 0; $i < 10000; $i++) {
        	$uuid = UUID::CreateV4();

            Assert::IsTrue(
                preg_match(self::PATTERN_UUID_V4, $uuid) === 1,
                'The UUIDv4 (create) is not valid - ' . $uuid
            );
        }
    }
    #endregion
}

$obj = new UtilTest('util');
UnitTest::RegisterTest($obj);
