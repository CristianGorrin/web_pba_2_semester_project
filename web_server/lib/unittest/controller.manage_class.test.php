<?php
namespace StudentCheckIn;
use ccg\unittesting\ITest;
use ccg\unittesting\Assert;
use ccg\unittesting\UnitTest;

require_once '../autoload.php';
require_once './unittest.php';

require './db_setup.php';

class ManageClassTest implements ITest {
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

    public function CreateClass() {
        Assert::IsTrue(
            is_int(ManageClasses::CreateClass('some_new_class')),
            'The create class failed...'
        );
        Assert::IsFalse(ManageClasses::CreateClass('some_new_class'), 'Create the same class...');
    }

    public function GetAllClass() {
        Assert::IsTrue(
            is_string(ManageClasses::GetAllClass()),
            "The result isn't a string"
        );
    }

    public function AssignStudentsToClass() {
        $list = array(1, 2);

        Assert::AreEqual(
            ManageClasses::AssignStudentsToClass(2, $list),
            $list,
            'The update failed...'
        );
    }

    public function AssingClassToSubject() {
        Assert::IsTrue(
            ManageClasses::AssingClassToSubject(4, 'test_subject_select'),
            "The assign failed..."
        );
    }

    public function GetClassLogInfo() {
        json_decode(ManageClasses::GetClassLogInfo(array(
            "94cc6e91-dea0-4bbe-8277-035454345397",
            "39903d35-7f41-474d-a03a-47cbd8fefc2f"
        )));

        Assert::IsTrue(json_last_error() == JSON_ERROR_NONE, "The result is not valid json...");
    }

}
$obj = new ManageClassTest('manage_class');
UnitTest::RegisterTest($obj);

