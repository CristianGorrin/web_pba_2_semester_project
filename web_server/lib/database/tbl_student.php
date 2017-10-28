<?php
namespace StudentCheckIn;

/**
 * Summary of TblStudent
 *
 * id int auto_increment primary key,
 * firstname nvarchar(128) not null,
 * surname nvarchar(128) not null,
 * email nvarchar(256) unique not null,
 * pass_hass nvarchar(255) not null,
 * validate bool default false,
 * class int not null,
 * device_uuid_v4 unique nvarchar(36),
 * cache_statistics text not null
 *
 * This is the entity class for TblStudent
 */
class TblStudent implements IEntity {
    public $id;
    public $firstname;
    public $surname;
    public $email;
    public $pass_hass;
    public $validate;
    public $class;
    public $device_uuid_v4;
    public $cache_statistics;

    /**
     * Summary of __construct
     * @param int $id
     * @param string $firstname
     * @param string $surname
     * @param string $email
     * @param string $pass_hass
     * @param bool $validate
     * @param int $class
     * @param string $device_uuid_v4
     * @param string $cache_statistics
     */
    public function __construct($id, $firstname, $surname, $email, $pass_hass, $validate, $class,
        $device_uuid_v4, $cache_statistics) {
        $this->id               = $id;
        $this->firstname        = $firstname;
        $this->surname          = $surname;
        $this->email            = $email;
        $this->pass_hass        = $pass_hass;
        $this->validate         = $validate;
        $this->class            = $class;
        $this->device_uuid_v4   = $device_uuid_v4;
        $this->cache_statistics = $cache_statistics;
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
        if (!is_string($this->firstname)) {
        	return false;
        }

        if (!is_string($this->surname)) {
        	return false;
        }

        if (!is_string($this->email)) {
        	return false;
        }

        if (!is_string($this->pass_hass)) {
        	return false;
        }

        if (!is_bool($this->validate)) {
        	return false;
        }

        if (!is_int($this->class)) {
        	return false;
        }

        if (!is_string($this->device_uuid_v4)) {
        	return false;
        }

        if (!is_string($this->cache_statistics)) {
        	return false;
        }

        return true;
    }
}

class RdgStudent implements IRDG {
    const _SELECT_BY = "select id, firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics from tbl_student where `%s` = %s;";
    const _UPDATE_BY = "update tbl_student set firstname = '%s', surname = '%s', email = '%s', pass_hass = '%s', validate = %s, class = '%s', device_uuid_v4 = '%s', cache_statistics  = '%s' where id = %s;";
    const _DELETE_BY = "delete from tbl_student where id = %s;";
    const _INSERT_BY = "insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('%s', '%s', '%s', '%s', %s, %s, '%s', '%s');";

    #region StudentCheckIn\IRDG Members
    /**
     * Insert the object into the table
     *
     * @param TblStudent $object
     */
    public static function Insret($object) {
        if (!$object->ValidateAsInsert()) {
        	return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_INSERT_BY,
                DatabaseCMD::EscapeString($object->firstname),
                DatabaseCMD::EscapeString($object->surname),
                DatabaseCMD::EscapeString($object->email),
                DatabaseCMD::EscapeString($object->pass_hass),
                $object->validate ? 'true' : 'false',
                DatabaseCMD::EscapeString($object->class),
                DatabaseCMD::EscapeString($object->device_uuid_v4),
                DatabaseCMD::EscapeString($object->cache_statistics)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblStudent $object
     */
    public static function Update($object) {
        if (!$object->ValidateAsUpdate()) {
            return false;
        }

        DatabaseCMD::ExecutedStatement(
            sprintf(
                self::_UPDATE_BY,
                DatabaseCMD::EscapeString($object->firstname),
                DatabaseCMD::EscapeString($object->surname),
                DatabaseCMD::EscapeString($object->email),
                DatabaseCMD::EscapeString($object->pass_hass),
                $object->validate ? 'true' : 'false',
                DatabaseCMD::EscapeString($object->class),
                DatabaseCMD::EscapeString($object->device_uuid_v4),
                DatabaseCMD::EscapeString($object->cache_statistics),
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
     * @return TblStudent|null
     */
    public static function Select($identifier) {
        return self::SelectBy('id', DatabaseCMD::EscapeString($identifier));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblStudent
     */
    public static function ResultToObject($input) {
        return new TblStudent(intval($input['id']), $input['firstname'], $input['surname'],
            $input['email'], $input['pass_hass'], boolval($input['validate']),
            $input['class'], $input['device_uuid_v4'], $input['cache_statistics']);
    }
    #endregion

    /**
     * Summary of SelectBy
     * A generic select
     *
     * @param string $by
     * @param mixed $value
     *
     * @return TblStudent
     */
    protected static function SelectBy($by, $value) {
        $result = DatabaseCMD::ExecutedStatement(sprintf(self::_SELECT_BY, $by, $value));
        return self::ResultToObject(mysqli_fetch_assoc($result));
    }

    /**
     * Summary of SelectByClass
     * select a row by class
     *
     * @param string $email
     * @return TblStudent
     */
    public static function SelectByEmail($email) {
        return self::SelectBy('email', sprintf("'%s'", DatabaseCMD::EscapeString($email)));
    }

    /**
     * Summary of SelectByDeviceUuid
     * @param string $uuid 
     * @return TblStudent
     */
    public static function SelectByDeviceUuid($uuid) {
        return self::SelectBy(
            'device_uuid_v4',
            sprintf("'%s'", DatabaseCMD::EscapeString($uuid))
        );
    }

    /**
     * Summary of SelectByPassHass
     * @param string $pass_hass
     * @return TblStudent
     */
    public static function SelectByPassHass($pass_hass) {
        return self::SelectBy(
            'pass_hass',
            sprintf("'%s'", DatabaseCMD::EscapeString($pass_hass))
        );
    }
}
