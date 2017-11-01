<?php
namespace StudentCheckIn;
/**
 * Summary of TblMetadata
 * `key`varchar(64) primary key not null,
 * `value` varchar(128) not null
 *
 * This is the entity class for TblMetadata
 */
class TblMetadata implements IEntity {
    public $key;
    public $value;

    /**
     * Summary of __construct
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value = '') {
        $this->key   = $key;
        $this->value = $value;
    }

    #region StudentCheckIn\IEntity Members
    function ValidateAsUpdate() {
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
        if (!\is_string($this->key)) {
            return false;
        }

        if (!\is_string($this->value)) {
        	return false;
        }

        return true;
    }
}

class RdgMetadata implements IRDG {
    const _SELECT_BY = "select `key`, `value` from tbl_metadata where `key` = '%s'";
    const _UPDATE_BY = "update tbl_metadata set `value` = '%s' where `key` = '%s'";
    const _DELETE_BY = "delete from tbl_metadata where `key` = '%s'";
    const _INSERT_BY = "insert into tbl_metadata  (`key`, `value`) values ('%s', '%s');";

    #region StudentCheckIn\IRDG Members
    /**
     * Insret the object into the table
     *
     * @param TblMetadata $object
     */
    public static function Insret($object) {
        if (!$object->ValidateAsInsert()) {
        	return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_INSERT_BY,
                DatabaseCMD::EscapeString($object->key),
                DatabaseCMD::EscapeString($object->value)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblMetadata $object
     */
    public static function Update($object) {
        if (!$object->ValidateAsUpdate()) {
            return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_UPDATE_BY,
                DatabaseCMD::EscapeString($object->value),
                DatabaseCMD::EscapeString($object->key)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Delete
     * Deletes a row from the table
     *
     * @param string $identifier
     * @return boolean
     */
    public static function Delete($identifier) {
        DatabaseCMD::ExecutedStatement(sprintf(self::_DELETE_BY, DatabaseCMD::EscapeString($identifier)));

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Select
     * Gets a row from the table or returens null
     *
     * @param string $identifier
     * @return TblMetadata|null
     */
    public static function Select($identifier) {
        $result = DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_SELECT_BY,
                DatabaseCMD::EscapeString($identifier)
            )
        );

        return self::ResultToObject(mysqli_fetch_assoc($result));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblMetadata
     */
    public static function ResultToObject($input) {
        if (is_null($input)) {
        	return null;
        }
        
        return new TblMetadata($input['key'], $input['value']);
    }
    #endregion
}
