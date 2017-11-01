<?php
namespace StudentCheckIn;

/**
 * Summary of TblRollCall
 *
 *	id int auto_increment primary key,
 *  class_log int not null,
 *  student int not null
 *
 * This is the entity class for TblRollCall
 */
class TblRollCall implements IEntity {
    public $id;
    public $class_log;
    public $student;
    public $latitude;
    public $longitude;

    /**
     * Summary of __construct
     * @param int $id
     * @param int $class_log
     * @param int $student
     * @param string $latitude
     * @param string $longitude
     */
    public function __construct($id, $class_log, $student, $latitude, $longitude) {
        $this->id        = $id;
        $this->class_log = $class_log;
        $this->student   = $student;
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
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
        if (!is_int($this->class_log)) {
        	return false;
        }

        if (!is_int($this->student)) {
        	return false;
        }

        if (!is_string($this->longitude)) {
        	return false;
        }

        if (!is_string($this->latitude)) {
        	return false;
        }

        return true;
    }
}

class RdgRollCall implements IRDG {
    const _SELECT_BY = "select id, class_log, student, latitude, longitude from tbl_roll_call where %s = %s;";
    const _UPDATE_BY = "update tbl_roll_call set class_log = %s, student = %s, latitude = %s, longitude = %s where id = %s;";
    const _DELETE_BY = "delete from tbl_roll_call where id = %s;";
    const _INSERT_BY = "insert into tbl_roll_call (class_log, student, latitude, longitude) values (%s, %s, %s, %s);";

    #region StudentCheckIn\IRDG Members
    /**
     * Insert the object into the table
     *
     * @param TblRollCall $object
     */
    public static function Insret($object) {
        if (!$object->ValidateAsInsert()) {
        	return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_INSERT_BY,
                DatabaseCMD::EscapeString($object->class_log),
                DatabaseCMD::EscapeString($object->student),
                DatabaseCMD::EscapeString($object->latitude),
                DatabaseCMD::EscapeString($object->longitude)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblRollCall $object
     */
    public static function Update($object) {
        if (!$object->ValidateAsUpdate()) {
            return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_UPDATE_BY,
                DatabaseCMD::EscapeString($object->class_log),
                DatabaseCMD::EscapeString($object->student),
                DatabaseCMD::EscapeString($object->latitude),
                DatabaseCMD::EscapeString($object->longitude),
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
     * @return TblRollCall|null
     */
    public static function Select($identifier) {
        return self::SelectBy('id', DatabaseCMD::EscapeString($identifier));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblRollCall
     */
    public static function ResultToObject($input) {
        if (is_null($input)) {
        	return null;
        }
        
        return new TblRollCall(intval($input['id']), intval($input['class_log']),
            intval($input['student']), $input['latitude'], $input['longitude']);
    }
    #endregion

    /**
     * Summary of SelectBy
     * A generic select
     *
     * @param string $by
     * @param mixed $value
     *
     * @return TblRollCall
     */
    protected static function SelectBy($by, $value) {
        $result = DatabaseCMD::ExecutedStatement(sprintf(self::_SELECT_BY, $by, $value));
        return self::ResultToObject(mysqli_fetch_assoc($result));
    }

    /**
     * Summary of SelectByClassLogAndStudent
     * @param int $class_log
     * @param int $student
     * @return TblRollCall
     */
    public static function SelectByClassLogAndStudent($class_log, $student) {
        return self::SelectBy(
            'class_log',
            sprintf(
                "%s and student = %s",
                DatabaseCMD::EscapeString($class_log),
                DatabaseCMD::EscapeString($student)
            )
        );
    }
}
