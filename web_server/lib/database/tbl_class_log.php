<?php
namespace StudentCheckIn;

/**
 * Summary of TblClassLog
 *
 * id int auto_increment primary key,
 * qr_code nvarchar(36) not null,
 * subject_class int not null,
 * teacher_by int not null,
 * unix_time int not null,
 * weight int default 1
 *
 * This is the entity class for TblClassLog
 */
class TblClassLog implements IEntity {
    public $id;
    public $qr_code;
    public $subject_class;
    public $teacher_by;
    public $unix_time;
    public $weight;

    /**
     * Summary of __construct
     * @param int $id
     * @param string $qr_code
     * @param int $subject_class
     * @param int $teacher_by
     * @param int $unix_time
     * @param int $weight
     */
    public function __construct($id, $qr_code, $subject_class, $teacher_by, $unix_time, $weight) {
        $this->id            = $id;
        $this->qr_code       = $qr_code;
        $this->subject_class = $subject_class;
        $this->teacher_by    = $teacher_by;
        $this->unix_time     = $unix_time;
        $this->weight        = $weight;
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
        if (!is_string($this->qr_code)) {
        	return false;
        }

        if (!is_int($this->subject_class)) {
        	return false;
        }

        if (!is_int($this->teacher_by)) {
        	return false;
        }

        if (!is_int($this->unix_time)) {
        	return false;
        }

        if (!is_int($this->weight)) {
        	return false;
        }

        return true;
    }
}

class RdgClassLog implements IRDG {
    const _SELECT_BY = "select id, qr_code, subject_class, teacher_by, unix_time, weight from tbl_class_log where %s = %s;";
    const _UPDATE_BY = "update tbl_class_log set qr_code = '%s', subject_class = %s, teacher_by = %s, unix_time = %s, weight = %s where id = %s;";
    const _DELETE_BY = "delete from tbl_class_log where id = %s;";
    const _INSERT_BY = "insert into tbl_class_log (qr_code, subject_class, teacher_by, unix_time, weight) values ('%s', %s, %s, %s, %s);";

    #region StudentCheckIn\IRDG Members
    /**
     * Insert the object into the table
     *
     * @param TblClassLog $object
     */
    public static function Insret($object) {
        if (!$object->ValidateAsInsert()) {
        	return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_INSERT_BY,
                DatabaseCMD::EscapeString($object->qr_code),
                DatabaseCMD::EscapeString($object->subject_class),
                DatabaseCMD::EscapeString($object->teacher_by),
                DatabaseCMD::EscapeString($object->unix_time),
                DatabaseCMD::EscapeString($object->weight)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblClassLog $object
     */
    public static function Update($object) {
        if (!$object->ValidateAsUpdate()) {
            return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_UPDATE_BY,
                DatabaseCMD::EscapeString($object->qr_code),
                DatabaseCMD::EscapeString($object->subject_class),
                DatabaseCMD::EscapeString($object->teacher_by),
                DatabaseCMD::EscapeString($object->unix_time),
                DatabaseCMD::EscapeString($object->weight),
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
     * @return TblClassLog|null
     */
    public static function Select($identifier) {
        return self::SelectBy('id', DatabaseCMD::EscapeString($identifier));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblClassLog
     */
    public static function ResultToObject($input) {
        return new TblClassLog(intval($input['id']), $input['qr_code'],
            intval($input['subject_class']), intval($input['teacher_by']),
            intval($input['unix_time']), intval($input['weight']));
    }
    #endregion

    /**
     * Summary of SelectBy
     * A generic select
     *
     * @param string $by
     * @param mixed $value
     *
     * @return TblClassLog
     */
    protected static function SelectBy($by, $value) {
        $result = DatabaseCMD::ExecutedStatement(sprintf(self::_SELECT_BY, $by, $value));
        return self::ResultToObject(mysqli_fetch_assoc($result));
    }

    /**
     * Summary of SelectByQrCode
     * @param string $qr_code 
     * @return TblClassLog
     */
    public static function SelectByQrCode($qr_code) {
        return self::SelectBy('qr_code', sprintf("'%s'", DatabaseCMD::EscapeString($qr_code)));
    }
}
