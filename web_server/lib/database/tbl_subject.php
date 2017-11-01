<?php
namespace StudentCheckIn;

/**
 * Summary of TblSubject
 *
 *  id int auto_increment primary key,
 *  `subject`int not null
 *
 * This is the entity class for TblSubject
 */
class TblSubject implements IEntity {
    public $id;
    public $subject;

    public function __construct($id, $subject) {
        $this->id      = $id;
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
        if (!is_string($this->subject)) {
        	return false;
        }

        return true;
    }
}

class RdgSubject implements IRDG {
    const _SELECT_BY = "select id, `subject` from tbl_subject where `%s` = %s;";
    const _UPDATE_BY = "update tbl_subject set `subject` = '%s' where id = %s;";
    const _DELETE_BY = "delete from tbl_subject where id = %s;";
    const _INSERT_BY = "insert into tbl_subject (`subject`) values ('%s');";

    #region StudentCheckIn\IRDG Members
    /**
     * Insert the object into the table
     *
     * @param TblSubject $object
     */
    public static function Insret($object) {
        if (!$object->ValidateAsInsert()) {
        	return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_INSERT_BY,
                DatabaseCMD::EscapeString($object->subject)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblSubject $object
     */
    public static function Update($object) {
        if (!$object->ValidateAsUpdate()) {
            return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_UPDATE_BY,
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
     * @return TblSubject|null
     */
    public static function Select($identifier) {
        return self::SelectBy('id', DatabaseCMD::EscapeString($identifier));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblSubject
     */
    public static function ResultToObject($input) {
        if (is_null($input)) {
        	return null;
        }

        return new TblSubject(intval($input['id']), $input['subject']);
    }
    #endregion

    /**
     * Summary of SelectBy
     * A generic select
     *
     * @param string $by
     * @param mixed $value
     *
     * @return TblSubject
     */
    protected static function SelectBy($by, $value) {
        $result = DatabaseCMD::ExecutedStatement(sprintf(self::_SELECT_BY, $by, $value));
        return self::ResultToObject(mysqli_fetch_assoc($result));
    }

    /**
     * Summary of SelectBySubject
     *
     * @param string $subject
     *
     * @return TblSubject
     */
    public static function SelectBySubject($subject) {
        return self::SelectBy('subject', sprintf("'%s'", DatabaseCMD::EscapeString($subject)));
    }
}
