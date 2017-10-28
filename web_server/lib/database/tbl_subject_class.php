<?php
namespace StudentCheckIn;

/**
 * Summary of TblSubjectClass
 *
 * id int auto_increment primary key,
 * class int not null,
 *`subject`int not null
 *
 * This is the entity class for TblSubjectClass
 */
class TblSubjectClass implements IEntity {
    public $id;
    public $class;
    public $subject;

    public function __construct($id, $class, $subject) {
        $this->id      = $id;
        $this->class   = $class;
        $this->subject = $subject;
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

        if (!is_int($this->subject)) {
        	return false;
        }

        return true;
    }
}

class RdgSubjectClass implements IRDG {
    const _SELECT_BY = "select id, class, `subject` from tbl_subject_class where %s = %s;";
    const _UPDATE_BY = "update tbl_subject_class set class = %s, `subject` = %s where id = %s;";
    const _DELETE_BY = "delete from tbl_subject_class where id = %s;";
    const _INSERT_BY = "insert into tbl_subject_class (class, `subject`) values (%s, %s);";

    #region StudentCheckIn\IRDG Members
    /**
     * Insert the object into the table
     *
     * @param TblSubjectClass $object
     */
    public static function Insret($object) {
        if (!$object->ValidateAsInsert()) {
        	return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_INSERT_BY,
                DatabaseCMD::EscapeString($object->class),
                DatabaseCMD::EscapeString($object->subject)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblSubjectClass $object
     */
    public static function Update($object) {
        if (!$object->ValidateAsUpdate()) {
            return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_UPDATE_BY,
                DatabaseCMD::EscapeString($object->class),
                DatabaseCMD::EscapeString($object->subject),
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
     * @return TblSubjectClass|null
     */
    public static function Select($identifier) {
        return self::SelectBy('id', DatabaseCMD::EscapeString($identifier));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblSubjectClass
     */
    public static function ResultToObject($input) {
        return new TblSubjectClass(intval($input['id']), intval($input['class']),
            intval($input['subject']));
    }
    #endregion

    /**
     * Summary of SelectBy
     * A generic select
     *
     * @param string $by
     * @param mixed $value
     *
     * @return TblSubjectClass
     */
    protected static function SelectBy($by, $value) {
        $result = DatabaseCMD::ExecutedStatement(sprintf(self::_SELECT_BY, $by, $value));
        return self::ResultToObject(mysqli_fetch_assoc($result));
    }

    /**
     * Summary of SelectByClassAndSubject
     * @param int $class
     * @param int $subject
     * @return TblSubjectClass
     */
    public static function SelectByClassAndSubject($class, $subject) {
        return self::SelectBy(
            'class', 
            sprintf(
                '%s and subject = %s', 
                DatabaseCMD::EscapeString($class), 
                DatabaseCMD::EscapeString($subject)
            )
        );
    }
}
