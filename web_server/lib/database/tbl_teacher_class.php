<?php
namespace StudentCheckIn;

/**
 * Summary of TblTeacherClass
 *
 * id int auto_increment primary key,
 * class int not null,
 * teacher int not null,
 *
 * This is the entity class for TblTeacherClass
 */
class TblTeacherClass implements IEntity {
    public $id;
    public $class;
    public $teacher;

    /**
     * Summary of __construct
     * @param int $id
     * @param int $class
     * @param int $teacher
     */
    public function __construct($id, $class, $teacher) {
        $this->id      = $id;
        $this->class   = $class;
        $this->teacher = $teacher;
    }

    #region StudentCheckIn\IEntity Members
    function ValidateAsUpdate() {
        if (!is_int($this->id)) {
        	return false;
        }

        return $this->Validate();
    }

    function ValidateAsInsert() {
        return $this->Validate();
    }
    #endregion

    /**
     * Summary of Validate
     * @return boolean
     */
    protected function Validate() {
        if (!is_int($this->class)) {
        	return false;
        }

        if (!is_int($this->teacher)) {
        	return false;
        }


        return true;
    }
}

class RdgTeacherClass implements IRDG {
    const _SELECT_BY = "select id, class, teacher from tbl_teacher_class where `%s` = %s;";
    const _UPDATE_BY = "update tbl_teacher_class set class = %s, teacher = %s where id = %s;";
    const _DELETE_BY = "delete from tbl_teacher_class where id = '%s';";
    const _INSERT_BY = "insert into tbl_teacher_class (class, teacher) values (%s, %s);";

    #region StudentCheckIn\IRDG Members
    /**
     * Insret the object into the table
     *
     * @param TblTeacherClass $object
     */
    public static function Insret($object) {
        if (!$object->ValidateAsInsert()) {
        	return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_INSERT_BY,
                DatabaseCMD::EscapeString($object->class),
                DatabaseCMD::EscapeString($object->teacher)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblTeacherClass $object
     */
    public static function Update($object) {
        if (!$object->ValidateAsUpdate()) {
            return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_UPDATE_BY,
                DatabaseCMD::EscapeString($object->class),
                DatabaseCMD::EscapeString($object->teacher),
                DatabaseCMD::EscapeString($object->id)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Delete
     * Deletes a row from the table
     *
     * @param int $identifier The id
     * @return boolean
     */
    public static function Delete($identifier) {
        DatabaseCMD::ExecutedStatement(
            sprintf(self::_DELETE_BY, DatabaseCMD::EscapeString($identifier))
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Select
     * Gets a row from the table or returens null
     *
     * @param int $identifier The id
     * @return TblTeacherClass|null
     */
    public static function Select($identifier) {
        return self::SelectBy('id', DatabaseCMD::EscapeString($identifier));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblTeacherClass
     */
    public static function ResultToObject($input) {
        if (is_null($input)) {
        	return null;
        }
        
        return new TblTeacherClass(intval($input['id']), intval($input['class']),
            intval($input['teacher']));
    }
    #endregion

    /**
     * Summary of SelectBy
     * A generic select
     *
     * @param string $by
     * @param mixed $value
     *
     * @return TblTeacherClass
     */
    protected static function SelectBy($by, $value) {
        $result = DatabaseCMD::ExecutedStatement(sprintf(self::_SELECT_BY, $by, $value));
        return self::ResultToObject(mysqli_fetch_assoc($result));
    }

    public static function SelectByClassAndTeacher($class, $teacher) {
        return self::SelectBy(
            'class',
            sprintf(
                "%s and teacher = %s",
                DatabaseCMD::EscapeString($class),
                DatabaseCMD::EscapeString($teacher)
            )
        );
    }
}
