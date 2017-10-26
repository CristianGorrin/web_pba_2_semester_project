<?php
namespace StudentCheckIn;
use ccg\unittesting\ITest;
use ccg\unittesting\Assert;
use ccg\unittesting\UnitTest;

require_once '../autoload.php';
require_once './unittest.php';

echo 'Setting the database up - pleas wait...';

$db_setup = file_get_contents('./db_setup.sql');
$db_data  = file_get_contents('./db_data.sql');

$db_cmd = mysqli_connect('127.0.0.1', 'dev', 'dev1234');

mysqli_multi_query($db_cmd, $db_setup . $db_data);
$e = mysqli_error($db_cmd);

do {
	if ($result = mysqli_store_result($db_cmd)) {
    	mysqli_free_result($result);
    }

} while(mysqli_next_result($db_cmd));

if (strlen($e) > 0) {
    throw new \Exception($e);
}

mysqli_close($db_cmd);
unset($db_data);
unset($db_data);

class DatabaseTest implements ITest {
    protected $test_name_identifier;
    protected $db_cmd;

    public function __construct($identifier) {
        $this->test_name_identifier = $identifier;
        $this->db_cmd = mysqli_connect('127.0.0.1', 'dev', 'dev1234', 'StudentCheckIn');
    }

    public function __destruct() {
        mysqli_close($this->db_cmd);
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

    protected function DbGet($table, $value, $key = 'id') {
        $sql = sprintf("select * from %s where `%s` = %s;", $table, $key, $value);
        return mysqli_fetch_assoc(mysqli_query($this->db_cmd, $sql));;
    }

    #region TblMtatdata
	public function TblMatadata_Insert() {
        $obj = new TblMetadata('test_key_insert', 'test_value_insert');

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgMetadata::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_metadata', "'test_key_insert'" , 'key');

        Assert::AreEqual(
            $db_result['key'],
            'test_key_insert',
            "The key isn't as expected"
        );

        Assert::AreEqual(
            $db_result['value'],
            'test_value_insert',
            "The value isn't as expected"
        );
    }

    public function TblMetadata_Update() {
        Assert::AreEqual(
            self::DbGet('tbl_metadata', "'test_key_update'" , 'key')['value'],
            'test_value',
            'The init value from the database is no as expected'
        );

        $obj = new TblMetadata('test_key_update', 'test_value_update');
        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgMetadata::Update($obj), 'The update has failed');

        Assert::AreEqual(
            self::DbGet('tbl_metadata', "'test_key_update'" , 'key')['value'],
            'test_value_update',
            'The value after the update from the database is no as expected'
        );
    }

    public function TblMetadata_Delete() {
        Assert::AreNotEqual(
            self::DbGet('tbl_metadata', "'test_key_delete'" , 'key'),
            null,
            "The value to be delete doesn't exist"
        );

        Assert::IsTrue(RdgMetadata::Delete('test_key_delete'), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_metadata', "'test_key_delete'" , 'key'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblMetadata_Select() {
        $temp = self::DbGet('tbl_metadata', "'test_key_select'" , 'key');

        Assert::AreEqual(
            $temp['key'],
            'test_key_select',
            "The database doesn't have the select row"
        );

        Assert::AreEqual(
            $temp['value'],
            'test_value',
            "The database doesn't have the select row"
        );

        $result = RdgMetadata::Select('test_key_select');

        Assert::AreEqual(
          $result->key,
          'test_key_select',
          "The key isn't as expected"
        );

        Assert::AreEqual(
            $result->value,
            'test_value',
            "The value isn't as expected"
        );
    }
    #endregion

    #region TblClass
    public function TblClass_Insert() {
        $obj = new TblClass(-1, 'test_class_insert');

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgClass::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_class', "5" , 'id');

        Assert::AreEqual(
            $db_result['id'],
            '5',
            "The id isn't as expected"
        );

        Assert::AreEqual(
            $db_result['class'],
            'test_class_insert',
            "The id isn't as expected"
        );
    }

    public function TblClass_Update() {
        Assert::AreEqual(
            self::DbGet('tbl_class', "1")['class'],
            'test_class_update',
            'The init value from the database is no as expected'
        );

        $obj = new TblClass(1, 'test_class_update_done');
        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgClass::Update($obj), 'The update has failed');

        Assert::AreEqual(
            self::DbGet('tbl_class', "1")['class'],
            'test_class_update_done',
            'The value after the update from the database is no as expected'
        );
    }

    public function TblClass_Delete() {
        Assert::AreNotEqual(
            self::DbGet('tbl_class', '2'),
            null,
            "The value to be delete doesn't exist"
        );

        Assert::IsTrue(RdgClass::Delete(2), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_class', '2'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblClass_Select() {
        $temp = self::DbGet('tbl_class', '3');
        Assert::AreEqual(
            $temp['id'],
            '3',
            "The database doesn't have the select row"
        );

        Assert::AreEqual(
            $temp['class'],
            'test_class_select',
            "The database doesn't have the select row"
        );

        $test = function($result, $type) {
            Assert::AreEqual(
                $result->id,
                3,
                "The key isn't as expected - Select by " . $type
            );

            Assert::AreEqual(
                $result->class,
                'test_class_select',
                "The key isn't as expected - Select by " . $type
            );
        };

        $test(RdgClass::Select(3), 'default');
        $test(RdgClass::SelectByClass('test_class_select'), 'class');
    }
    #endregion

    #region TblStudent
    public function TblStudent_Insert() {
        $obj = new TblStudent(-1, 'test_firstename_insert', 'test_surname_insert',
            'test_email_insert', 'test_pass_hass_insert', false, 4, 'test_device_uuid_v4_insert',
            'test_cache_statistics_insert');

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgStudent::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_student', "5" , 'id');

        Assert::AreEqual(
            $db_result['id'],
            '5',
            "The id isn't as expected"
        );

        Assert::AreEqual(
            $db_result['firstname'],
            'test_firstename_insert',
            "The firstname isn't as expected"
        );

        Assert::AreEqual(
            $db_result['surname'],
            'test_surname_insert',
            "The surname isn't as expected"
        );

        Assert::AreEqual(
            $db_result['email'],
            'test_email_insert',
            "The email isn't as expected"
        );

        Assert::AreEqual(
            $db_result['pass_hass'],
            'test_pass_hass_insert',
            "The pass_hass isn't as expected"
        );

        Assert::AreEqual(
            $db_result['validate'],
            '0',
            "The validate isn't as expected"
        );

        Assert::AreEqual(
            $db_result['class'],
            '4',
            "The class isn't as expected"
        );

        Assert::AreEqual(
            $db_result['device_uuid_v4'],
            'test_device_uuid_v4_insert',
            "The device_uuid_v4 isn't as expected"
        );

        Assert::AreEqual(
            $db_result['cache_statistics'],
            'test_cache_statistics_insert',
            "The cache_statistics isn't as expected"
        );
    }

    public function TblStudent_Update() {
        $db_result     = self::DbGet('tbl_student', '3');
        $test_db_value = function($column, $value, $expected, $init = true) {
            Assert::AreEqual(
                $expected[$column],
                $value,
                sprintf(
                    $init ?
                        'The init value from the database is not as expected - %s' :
                        'The value after the update from the database is not as expected - %s'
               , $column)
            );
        };

        $test_db_value('id', '3', $db_result);
        $test_db_value('firstname', 'test_firstename_update', $db_result);
        $test_db_value('surname', 'test_surname_update', $db_result);
        $test_db_value('email', 'test_email_update', $db_result);
        $test_db_value('pass_hass', 'test_pass_hass_update', $db_result);
        $test_db_value('validate', '0', $db_result);
        $test_db_value('class', '4', $db_result);
        $test_db_value('device_uuid_v4', 'test_device_uuid_v4_update', $db_result);
        $test_db_value('cache_statistics', 'test_cache_statistics_update', $db_result);

        $obj = new TblStudent(3, 'test_firstename_update_done', 'test_surname_update_done',
            'test_email_update_done', 'test_pass_hass_update_done', true, 4,
            'test_device_uuid_v4_update_done', 'test_cache_statistics_update_done');

        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgStudent::Update($obj), 'The update has failed');

        $db_result = self::DbGet('tbl_student', '3');

        $test_db_value('id', '3', $db_result, false);
        $test_db_value('firstname', 'test_firstename_update_done', $db_result, false);
        $test_db_value('surname', 'test_surname_update_done', $db_result, false);
        $test_db_value('email', 'test_email_update_done', $db_result, false);
        $test_db_value('pass_hass', 'test_pass_hass_update_done', $db_result, false);
        $test_db_value('validate', '1', $db_result, false);
        $test_db_value('class', '4', $db_result, false);
        $test_db_value('device_uuid_v4', 'test_device_uuid_v4_update_done', $db_result, false);
        $test_db_value('cache_statistics', 'test_cache_statistics_update_done', $db_result,
            false);
    }

    public function TblStudent_Delete() {
        Assert::AreNotEqual(
            self::DbGet('tbl_student', '1'),
            null,
            "The value to be delete doesn't exist"
        );

        Assert::IsTrue(RdgClass::Delete(1), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_class', '1'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblStudent_Select() {
        Assert::AreEqual(
            self::DbGet('tbl_student', '2')['id'],
            '2',
            "The database doesn't have the row"
        );

        $test_db = function($value, $type) {
            Assert::AreEqual(
                $value->id,
                2,
                "The id isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->firstname,
                'test_firstename_select',
                "The firstname isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->surname,
                'test_surname_select',
                "The surname isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->email,
                'test_email_select',
                "The email isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->pass_hass,
                'test_pass_hass_select',
                "The pass_hass isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->validate,
                false,
                "The validate isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->class,
                '4',
                "The class isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->device_uuid_v4,
                'test_device_uuid_v4_select',
                "The device_uuid_v4 isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->cache_statistics,
                'test_cache_statistics_select',
                "The cache_statistics isn't as expected - " . $type
            );
        };

        $test_db(RdgStudent::Select(2), 'default');
        $test_db(RdgStudent::SelectByDeviceUuid('test_device_uuid_v4_select'), 'Device UUID');
        $test_db(RdgStudent::SelectByEmail('test_email_select'), 'Email');
    }
    #endregion
}

$obj = new DatabaseTest('db');
UnitTest::RegisterTest($obj);
