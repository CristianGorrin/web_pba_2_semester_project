<?php
namespace StudentCheckIn;
use ccg\unittesting\ITest;
use ccg\unittesting\Assert;
use ccg\unittesting\UnitTest;

require_once '../autoload.php';
require_once './unittest.php';

require './db_setup.php';

class AccTeacherTest implements ITest {
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

    public function Singup() {
        $result = AccStudent::Singup('singup_name', 'singup_surname', 'singu@email.com',
            'singup_password', 1);

        Assert::IsTrue(is_string($result), 'The student sing up failed...');
    }

    public function VerifyPassword() {
        Assert::IsTrue(
            AccStudent::VerifyPassword('test_email_delete', 'new_password'),
            "Can't verify password..."
        );

        Assert::IsFalse(
            AccStudent::VerifyPassword('test_email_delete', 'some_new_password' . 'more'),
            'The password was verify with a wrong string...'
        );
    }

    public function UpdatePasswrod() {
        Assert::IsTrue(
            AccStudent::UpdatePassword(
                'some_new_password',
                'the_new_password',
                'test_email_fk_alt'
            ),
            "Can't update password..."
        );

        $obj = RdgStudent::SelectByEmail('test_email_fk_alt');

        Assert::IsTrue(
            password_verify('the_new_password', $obj->pass_hass),
            'The password update failed...'
        );
    }

    public function ValidateAcc() {
        Assert::IsTrue(
            AccStudent::ValidateAcc('test_email_delete'),
            'The validate account failed...'
        );

        $obj = RdgStudent::SelectByEmail('test_email_delete');

        Assert::IsTrue($obj->validate, "The account wasn't validated");
    }

    public function PairDevice() {
        $result = AccStudent::PairDevice(1);

        Assert::IsTrue(is_string($result), 'The pair device failed...');

        $obj = RdgStudent::SelectByDeviceUuid($result);
        Assert::IsFalse(is_null($obj), "The new device UUID isn't save to the database");
    }

    public function IsDeviceOf() {
        Assert::IsTrue(
            AccStudent::IsDeviceOf('test_email_fk_alt', 'c474cd60-efd8-4f3b-a015-d81f9f7fa87c'),
            'The IsDeviceOf function (valid UUID) failed...'
        );

        Assert::IsFalse(
            AccStudent::IsDeviceOf('test_email_fk_alt', '01b063f3-38b0-415a-8b0a-ab85aeaaa9f9'),
            'The IsDeviceOf function (not valid UUID) failed...'
        );
    }
}

$obj = new AccTeacherTest('Student');
UnitTest::RegisterTest($obj);
