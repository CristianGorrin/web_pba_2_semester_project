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
    protected $acc;

    public function __construct($identifier) {
        $this->test_name_identifier = $identifier;
        $this->acc = array(
            'first_name' => 'test_acc_name',
            'surname'    => 'test_acc_surname',
            'email'      => 'test_acc@email.com',
            'password'   => 'test_acc_pass'
        );
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

    public function Sinup() {
        Assert::IsTrue(
            AccTeacher::Singup($this->acc['first_name'], $this->acc['surname'],
                $this->acc['email'], $this->acc['password']),
            'The sing up failed...'
        );
    }

    public function VerifyPassword() {
        Assert::IsTrue(
            AccTeacher::VerifyPassword('test_acc_email', 'some_new_password'),
            "Can't verify password"
        );

        Assert::IsFalse(
            AccTeacher::VerifyPassword('test_acc_email', 'some_new_password' . 'more'),
            'The password was verify with a wrong string'
        );
    }

    public function UpdatePassword() {
        Assert::IsTrue(
            AccTeacher::UpdatePassword(
                'lala_some_text',
                'test_acc_pass_new',
                'test_eamil_delete'
            ),
            "Can't update password..."
        );

        $obj = RdgTeacher::SelectByEmail('test_eamil_delete');
        Assert::IsTrue(
            password_verify('test_acc_pass_new', $obj->pass_hass),
            'The password update failed...'
        );
    }
}

$obj = new AccTeacherTest('Teacher');
UnitTest::RegisterTest($obj);
