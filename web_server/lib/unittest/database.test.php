<?php
namespace StudentCheckIn;
use ccg\unittesting\ITest;
use ccg\unittesting\Assert;
use ccg\unittesting\UnitTest;

require_once '../autoload.php';
require_once './unittest.php';

echo 'Setting the database up - pleas wait...' . PHP_EOL;

$db_setup = file_get_contents('./db_setup.sql');
$db_data  = file_get_contents('./db_data.sql');

$db_cmd = mysqli_connect('127.0.0.1', 'dev', 'dev1234');

mysqli_multi_query($db_cmd, $db_setup . $db_data);

do {
	if ($result = mysqli_store_result($db_cmd)) {
    	mysqli_free_result($result);
    }

    $e = mysqli_error($db_cmd);

    if (strlen($e) > 0) {
        throw new \Exception($e);
    }
} while(mysqli_next_result($db_cmd));
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

    protected static function TestDBValue($column, $expected, $value, $init = true) {
        Assert::AreEqual(
            $value[$column],
            $expected,
            sprintf(
                $init ?
                    'The init value from the database is not as expected - %s' :
                    'The value after the update from the database is not as expected - %s',
                $column
            )
        );
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

        $obj = new TblMetadata('test_key_update', 'test_value_update_done');
        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgMetadata::Update($obj), 'The update has failed');

        Assert::AreEqual(
            self::DbGet('tbl_metadata', "'test_key_update'" , 'key')['value'],
            'test_value_update_done',
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

        $db_result = self::DbGet('tbl_class', "6" , 'id');

        Assert::AreEqual(
            $db_result['id'],
            '6',
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

        $db_result = self::DbGet('tbl_student', "6" , 'id');

        Assert::AreEqual(
            $db_result['id'],
            '6',
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

        self::TestDBValue('id', '3', $db_result);
        self::TestDBValue('firstname', 'test_firstename_update', $db_result);
        self::TestDBValue('surname', 'test_surname_update', $db_result);
        self::TestDBValue('email', 'test_email_update', $db_result);
        self::TestDBValue('pass_hass', 'test_pass_hass_update', $db_result);
        self::TestDBValue('validate', '0', $db_result);
        self::TestDBValue('class', '4', $db_result);
        self::TestDBValue('device_uuid_v4', 'test_device_uuid_v4_update', $db_result);
        self::TestDBValue('cache_statistics', 'test_cache_statistics_update', $db_result);

        $obj = new TblStudent(3, 'test_firstename_update_done', 'test_surname_update_done',
            'test_email_update_done', 'test_pass_hass_update_done', true, 4,
            'test_device_uuid_v4_update_done', 'test_cache_statistics_update_done');

        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgStudent::Update($obj), 'The update has failed');

        $db_result = self::DbGet('tbl_student', '3');

        self::TestDBValue('id', '3', $db_result, false);
        self::TestDBValue('firstname', 'test_firstename_update_done', $db_result, false);
        self::TestDBValue('surname', 'test_surname_update_done', $db_result, false);
        self::TestDBValue('email', 'test_email_update_done', $db_result, false);
        self::TestDBValue('pass_hass', 'test_pass_hass_update_done', $db_result, false);
        self::TestDBValue('validate', '1', $db_result, false);
        self::TestDBValue('class', '4', $db_result, false);
        self::TestDBValue('device_uuid_v4', 'test_device_uuid_v4_update_done', $db_result, false);
        self::TestDBValue('cache_statistics', 'test_cache_statistics_update_done', $db_result,
            false);
    }

    public function TblStudent_Delete() {
        Assert::AreNotEqual(
            self::DbGet('tbl_student', '1'),
            null,
            "The value to be delete doesn't exist"
        );

        Assert::IsTrue(RdgStudent::Delete(1), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_student', '1'),
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
        $test_db(RdgStudent::SelectByPassHass('test_pass_hass_select'), 'Pass hass');
    }
    #endregion

    #region TblTeacher
    public function TblTeacher_Insert() {
        $obj = new TblTeacher(-1, 'test_firstname_insert', 'test_surname_insert',
            'test_email_insert', 'test_pass_hass_insert');

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgTeacher::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_teacher', "6" , 'id');
        Assert::AreEqual(
            $db_result['id'],
            '6',
            "The id isn't as expected"
        );

        Assert::AreEqual(
            $db_result['firstname'],
            'test_firstname_insert',
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
            $db_result['hass_pass'],
            'test_pass_hass_insert',
            "The pass_hass isn't as expected"
        );
    }

    public function TblTeacher_Update() {
        $db_result = self::DbGet('tbl_teacher', "2");

        self::TestDBValue('id', '2', $db_result);
        self::TestDBValue('firstname', 'test_firstname_udapte', $db_result);
        self::TestDBValue('surname', 'test_surname_update', $db_result);
        self::TestDBValue('email', 'test_eamil_update', $db_result);
        self::TestDBValue('hass_pass', 'test_hass_pass_update', $db_result);

        $obj = new TblTeacher(2, 'test_firstname_udapte_done', 'test_surname_update_done',
            'test_eamil_update_done', 'test_hass_pass_update_done');

        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgTeacher::Update($obj), 'The update has failed');

        $db_result = self::DbGet('tbl_teacher', '2');

        self::TestDBValue('id', '2', $db_result);
        self::TestDBValue('firstname', 'test_firstname_udapte_done', $db_result, false);
        self::TestDBValue('surname', 'test_surname_update_done', $db_result, false);
        self::TestDBValue('email', 'test_eamil_update_done', $db_result, false);
        self::TestDBValue('hass_pass', 'test_hass_pass_update_done', $db_result, false);
    }

    public function TblTeacher_Delete() {
        Assert::AreNotEqual(
           self::DbGet('tbl_teacher', '3'),
           null,
           "The value to be delete doesn't exist"
       );

        Assert::IsTrue(RdgTeacher::Delete(3), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_teacher', '3'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblTeacher_Select() {
        Assert::AreEqual(
            self::DbGet('tbl_teacher', '1')['id'],
            '1',
            "The database doesn't have the row"
        );

        $test_db = function($value, $type) {
            Assert::AreEqual(
                $value->id,
                1,
                "The id isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->firstname,
                'test_firstname_select',
                "The firstname isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->surname,
                'test_surname_select',
                "The surname isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->email,
                'test_eamil_select',
                "The email isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->pass_hass,
                'test_hass_pass_select',
                "The hass_pass isn't as expected - " . $type
            );
        };

        $test_db(RdgTeacher::Select(1), 'default');
        $test_db(RdgTeacher::SelectByEmail('test_eamil_select'), 'email');
        $test_db(RdgTeacher::SelectByHassPass('test_hass_pass_select'), 'hass pass');
    }
    #endregion

    #region TblSubject
    public function TblSubject_Insert() {
        $obj = new TblSubject(-1, 'test_subject_insert');

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgSubject::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_subject', "7" , 'id');
        Assert::AreEqual(
            $db_result['id'],
            '7',
            "The id isn't as expected"
        );

        Assert::AreEqual(
            $db_result['subject'],
            'test_subject_insert',
            "The subject isn't as expected"
        );
    }

    public function TblSubject_Update() {
        $db_result = self::DbGet('tbl_subject', "2");

        self::TestDBValue('id', '2', $db_result);
        self::TestDBValue('subject', 'test_subject_update', $db_result);

        $obj = new TblSubject(2, 'test_subject_update_done');

        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgSubject::Update($obj), 'The update has failed');

        $db_result = self::DbGet('tbl_subject', "2");

        self::TestDBValue('id', '2', $db_result, false);
        self::TestDBValue('subject', 'test_subject_update_done', $db_result, false);
    }

    public function TblSubject_Delete() {
        Assert::AreNotEqual(
            self::DbGet('tbl_subject', '3'),
            null,
            "The value to be delete doesn't exist"
        );

        Assert::IsTrue(RdgSubject::Delete(3), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_subject', '3'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblSubject_Select() {
        Assert::AreEqual(
            self::DbGet('tbl_teacher', '1')['id'],
            '1',
            "The database doesn't have the row"
        );

        $test_db = function($value, $type) {
            Assert::AreEqual(
                $value->id,
                1,
                "The id isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->subject,
                'test_subject_select',
                "The id isn't as expected - " . $type
            );
        };

        $test_db(RdgSubject::Select(1), 'default');
        $test_db(RdgSubject::SelectBySubject('test_subject_select'), 'subject');
    }
    #endregion

    #region tbl_subject_class
    public function TblSubjectClass_Insert() {
        $obj = new TblSubjectClass(-1, 5, 6);

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgSubjectClass::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_subject_class', "5" , 'id');
        Assert::AreEqual(
            $db_result['id'],
            '5',
            "The id isn't as expected"
        );

        Assert::AreEqual(
            $db_result['class'],
            '5',
            "The class isn't as expected"
        );

        Assert::AreEqual(
            $db_result['subject'],
            '6',
            "The class isn't as expected"
        );
    }

    public function TblSubjectClass_Update() {
        $db_result = self::DbGet('tbl_subject_class', "1");

        self::TestDBValue('id', '1', $db_result);
        self::TestDBValue('class', '4', $db_result);
        self::TestDBValue('subject', '4', $db_result);

        $obj = new TblSubjectClass(1, 5, 5);

        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgSubjectClass::Update($obj), 'The update has failed');

        $db_result = self::DbGet('tbl_subject_class', "1");

        self::TestDBValue('id', '1', $db_result, false);
        self::TestDBValue('class', '5', $db_result, false);
        self::TestDBValue('subject', '5', $db_result, false);
    }

    public function TblSubjectClass_Delete() {
        Assert::AreNotEqual(
           self::DbGet('tbl_subject_class', '2'),
           null,
           "The value to be delete doesn't exist"
       );

        Assert::IsTrue(RdgSubjectClass::Delete(2), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_subject_class', '2'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblSubjectClass_Select() {
        Assert::AreEqual(
           self::DbGet('tbl_subject_class', '3')['id'],
           '3',
           "The database doesn't have the row"
        );

        $test_db = function($value, $type) {
            Assert::AreEqual(
                $value->id,
                3,
                "The id isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->class,
                4,
                "The class isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->subject,
                5,
                "The subject isn't as expected - " . $type
            );
        };

        $test_db(RdgSubjectClass::Select(3), 'default');
        $test_db(RdgSubjectClass::SelectByClassAndSubject(4, 5), 'class and subject');
    }
    #endregion

    #region TblClassLog
    public function TblClassLog_Insert() {
        $time = time();

        $obj = new TblClassLog(-1, '6458e19f-c47f-4366-b9aa-e1d6067e38b3', 5, 4, $time, 12);

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgClassLog::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_class_log', "6" , 'id');
        Assert::AreEqual(
            $db_result['id'],
            '6',
            "The id isn't as expected"
        );

        Assert::AreEqual(
            $db_result['qr_code'],
            '6458e19f-c47f-4366-b9aa-e1d6067e38b3',
            "The qr_code isn't as expected"
        );

        Assert::AreEqual(
            $db_result['subject_class'],
            '5',
            "The subject_class isn't as expected"
        );

        Assert::AreEqual(
            $db_result['teacher_by'],
            '4',
            "The teacher_by isn't as expected"
        );

        Assert::AreEqual(
            $db_result['unix_time'],
            strval($time),
            "The unix_time isn't as expected"
        );

        Assert::AreEqual(
            $db_result['weight'],
            '12',
            "The unix_time isn't as expected"
        );
    }

    public function TblClassLog_Update() {
        $db_result = self::DbGet('tbl_class_log', "1");

        self::TestDBValue('id', '1', $db_result);
        self::TestDBValue('qr_code', 'dbe9ed21-2c61-4937-95c8-5656975e8c1d', $db_result);
        self::TestDBValue('subject_class', '3', $db_result);
        self::TestDBValue('teacher_by', '5', $db_result);
        self::TestDBValue('unix_time', '1509129410', $db_result);
        self::TestDBValue('weight', '1', $db_result);

        $time = time();
        $obj = new TblClassLog(1, '38b09f05-da77-483b-a5b7-2e570b3c0d86', 4, 4, $time, 56);

        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgClassLog::Update($obj), 'The update has failed');

        $db_result = self::DbGet('tbl_class_log', "1");

        self::TestDBValue('id', '1', $db_result, false);
        self::TestDBValue('qr_code', '38b09f05-da77-483b-a5b7-2e570b3c0d86', $db_result, false);
        self::TestDBValue('subject_class', '4', $db_result, false);
        self::TestDBValue('teacher_by', '4', $db_result, false);
        self::TestDBValue('unix_time', strval($time), $db_result, false);
        self::TestDBValue('weight', '56', $db_result, false);
    }

    public function TblClassLog_Delete() {
        Assert::AreNotEqual(
           self::DbGet('tbl_class_log', '2'),
           null,
           "The value to be delete doesn't exist"
       );

        Assert::IsTrue(RdgClassLog::Delete(2), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_class_log', '2'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblClassLog_Select() {
        Assert::AreEqual(
           self::DbGet('tbl_class_log', '3')['id'],
           '3',
           "The database doesn't have the row"
        );

        $test_db = function($value, $type) {
            Assert::AreEqual(
                $value->id,
                3,
                "The id isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->qr_code,
                '39903d35-7f41-474d-a03a-47cbd8fefc2f',
                "The qr_code isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->subject_class,
                3,
                "The subject_class isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->teacher_by,
                5,
                "The teacher_by isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->unix_time,
                1509129412,
                "The unix_time isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->weight,
                3,
                "The weight isn't as expected - " . $type
            );
        };

        $test_db(RdgClassLog::Select(3), 'default');
        $test_db(RdgClassLog::SelectByQrCode('39903d35-7f41-474d-a03a-47cbd8fefc2f'), 'qr_code');
    }
    #endregion

    #region tblTeacherClass
    public function TblTeacherClass_Insert() {
        $obj = new TblTeacherClass(-1, 4, 5);

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgTeacherClass::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_teacher_class', '4');

        Assert::AreEqual(
            $db_result['id'],
            '4',
            "The id isn't as expected"
        );

        Assert::AreEqual(
            $db_result['class'],
            '4',
            "The class isn't as expected"
        );

        Assert::AreEqual(
            $db_result['teacher'],
            '5',
            "The teacher isn't as expected"
        );
    }

    public function TblTeacherClass_Update() {
        $db_result = self::DbGet('tbl_teacher_class', "1");

        self::TestDBValue('id', '1', $db_result);
        self::TestDBValue('class', '4', $db_result);
        self::TestDBValue('teacher', '4', $db_result);

        $obj = new TblTeacherClass(1, 5, 4);

        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgTeacherClass::Update($obj), 'The update has failed');

        $db_result = self::DbGet('tbl_teacher_class', "1");

        self::TestDBValue('id', '1', $db_result, false);
        self::TestDBValue('class', '5', $db_result, false);
        self::TestDBValue('teacher', '4', $db_result, false);
    }

    public function TblTeacherClass_Delete() {
        Assert::AreNotEqual(
           self::DbGet('tbl_teacher_class', '2'),
           null,
           "The value to be delete doesn't exist"
       );

        Assert::IsTrue(RdgTeacherClass::Delete(2), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_teacher_class', '2'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblTeacherClass_Select() {
        Assert::AreEqual(
           self::DbGet('tbl_teacher_class', '3')['id'],
           '3',
           "The database doesn't have the row"
        );

        $test_db = function($value, $type) {
            Assert::AreEqual(
                $value->id,
                3,
                "The id isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->class,
                5,
                "The class isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->teacher,
                1,
                "The teacher isn't as expected - " . $type
            );
        };

        $test_db(RdgTeacherClass::Select(3), 'default');
        $test_db(RdgTeacherClass::SelectByClassAndTeacher(5, 1), 'class and teacher');
    }
    #endregion

    #region TblRollCall
    public function TblRollCall_Insert() {
        $obj = new TblRollCall(-1, 4, 2);

        Assert::IsTrue($obj->ValidateAsInsert(), 'The values can be used for a insert');

        $result     = RdgRollCall::Insret($obj);
        $last_error = DatabaseCMD::GetErrorMessage();

        Assert::IsTrue($result, 'The insert into the database failed [' . $last_error . ']');

        $db_result = self::DbGet('tbl_roll_call', '4');

        Assert::AreEqual(
            $db_result['id'],
            '4',
            "The id isn't as expected"
        );

        Assert::AreEqual(
            $db_result['class_log'],
            '4',
            "The class_log isn't as expected"
        );

        Assert::AreEqual(
            $db_result['student'],
            '2',
            "The student isn't as expected"
        );
    }

    public function TblRollCall_Update() {
        $db_result = self::DbGet('tbl_roll_call', "1");

        self::TestDBValue('id', '1', $db_result);
        self::TestDBValue('class_log', '5', $db_result);
        self::TestDBValue('student', '2', $db_result);

        $obj = new TblRollCall(1, 4, 5);

        Assert::IsTrue($obj->ValidateAsUpdate(), "The object can't be used for an update");
        Assert::IsTrue(RdgRollCall::Update($obj), 'The update has failed');

        $db_result = self::DbGet('tbl_roll_call', "1");

        self::TestDBValue('id', '1', $db_result, false);
        self::TestDBValue('class_log', '4', $db_result, false);
        self::TestDBValue('student', '5', $db_result, false);
    }

    public function TblRollCall_Delete() {
        Assert::AreNotEqual(
            self::DbGet('tbl_roll_call', '2'),
            null,
            "The value to be delete doesn't exist"
        );

        Assert::IsTrue(RdgRollCall::Delete(2), 'The delete failed');

        Assert::AreEqual(
            self::DbGet('tbl_roll_call', '2'),
            null,
            "The value wasn't delete"
        );
    }

    public function TblRollCall_Select() {
        Assert::AreEqual(
           self::DbGet('tbl_roll_call', '3')['id'],
           '3',
           "The database doesn't have the row"
        );

        $test_db = function($value, $type) {
            Assert::AreEqual(
                $value->id,
                3,
                "The id isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->class_log,
                5,
                "The class_log isn't as expected - " . $type
            );

            Assert::AreEqual(
                $value->student,
                5,
                "The student isn't as expected - " . $type
            );
        };

        $test_db(RdgRollCall::Select(3), 'default');
        $test_db(RdgRollCall::SelectByClassLogAndStudent(5, 5), 'class log and student');
    }
    #endregion
}

$obj = new DatabaseTest('db');
UnitTest::RegisterTest($obj);
