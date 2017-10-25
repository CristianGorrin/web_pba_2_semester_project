<?php
namespace StudentCheckIn;
use ccg\unittesting\ITest;
use ccg\unittesting\Assert;
use ccg\unittesting\UnitTest;

require_once '../autoload.php';
require_once './unittest.php';

$db_setup = file_get_contents('./db_setup.sql');
$db_data  = file_get_contents('./db_data.sql');

$db_cmd = mysqli_connect('127.0.0.1', 'dev', 'dev1234');

mysqli_multi_query($db_cmd, $db_setup . $db_data);
$e = mysqli_error($db_cmd);
if (strlen($e) > 0) {
    throw new \Exception($e);
}
mysqli_close($db_cmd);
unset($db_data);
unset($db_data);
sleep(2);


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
        Assert::IsTrue(RdgMetadata::Update($obj), 'The update as failed');

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

    public function TblClass_Insert() {
        $obj = new TblClass(-1, 'test_class_insert');

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgClass::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_class', "4" , 'id');

        Assert::AreEqual(
            $db_result['id'],
            '4',
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
        Assert::IsTrue(RdgClass::Update($obj), 'The update as failed');

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
}

$obj = new DatabaseTest('db');
UnitTest::RegisterTest($obj);
