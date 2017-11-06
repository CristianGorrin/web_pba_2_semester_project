<?php
namespace StudentCheckIn;

/**
 * Summary of TblClass
 *
 * id int auto_increment primary key,
 * class nvarchar(32) unique not null
 *
 * This is the entity class for TblClass
 */
class TblClass implements IEntity {
    public $id;
    public $class;

    /**
     * Summary of __construct
     * @param int $id
     * @param string $class
     */
    public function __construct($id, $class) {
        $this->id    = $id;
        $this->class = $class;
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
        if (!is_string($this->class)) {
        	return false;
        }

        return true;
    }
}

class RdgClass implements IRDG {
    const _SELECT_BY = "select id, class from tbl_class where `%s` = %s";
    const _UPDATE_BY = "update tbl_class set class = '%s' where id = %s";
    const _DELETE_BY = "delete from tbl_class where id = '%s'";
    const _INSERT_BY = "insert into tbl_class (class) values ('%s');";

    const _SELECT_ALL = "select id, class from tbl_class;";

    #region StudentCheckIn\IRDG Members
    /**
     * Insret the object into the table
     *
     * @param TblClass $object
     */
    public static function Insret($object) {
        if (!$object->ValidateAsInsert()) {
        	return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_INSERT_BY,
                DatabaseCMD::EscapeString($object->class)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblClass $object
     */
    public static function Update($object) {
        if (!$object->ValidateAsUpdate()) {
            return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_UPDATE_BY,
                DatabaseCMD::EscapeString($object->class),
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
     * @return TblClass|null
     */
    public static function Select($identifier) {
        return self::SelectBy('id', DatabaseCMD::EscapeString($identifier));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblClass
     */
    public static function ResultToObject($input) {
        if (is_null($input)) {
        	return null;
        }

        return new TblClass(intval($input['id']), $input['class']);
    }
    #endregion

    /**
     * Summary of SelectBy
     * A generic select
     *
     * @param string $by
     * @param mixed $value
     *
     * @return TblClass
     */
    protected static function SelectBy($by, $value) {
        $result = DatabaseCMD::ExecutedStatement(sprintf(self::_SELECT_BY, $by, $value));
        return self::ResultToObject(mysqli_fetch_assoc($result));
    }

    /**
     * Summary of SelectByClass
     * select a row by class
     *
     * @param string $class
     * @return TblClass
     */
    public static function SelectByClass($class) {
        return self::SelectBy('class', sprintf("'%s'", DatabaseCMD::EscapeString($class)));
    }

    /**
     * Summary of GetAll
     * Get all class
     * 
     * @return TblClass[]
     * @yield
     */
    public static function GetAll() {
        $result = DatabaseCMD::ExecutedStatement(self::_SELECT_ALL);

        foreach ($result as $value) {
            yield self::ResultToObject($value);
        }
    }
}
