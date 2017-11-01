<?php
namespace StudentCheckIn;

/**
 * Summary of TblTeacher
 *
 * id int auto_increment primary key,
 * firstname nvarchar(128) not null,
 * surname nvarchar(128) not null,
 * email nvarchar(256) not null,
 * hass_pass nvarchar(255) not null
 *
 * This is the entity class for TblTeacher
 */
class TblTeacher implements IEntity {
    public $id;
    public $firstname;
    public $surname;
    public $email;
    public $pass_hass;

    /**
     * Summary of __construct
     * @param int $id
     * @param string $firstname
     * @param string $surname
     * @param string $email
     * @param string $pass_hass
     */
    public function __construct($id, $firstname, $surname, $email, $pass_hass) {
        $this->id               = $id;
        $this->firstname        = $firstname;
        $this->surname          = $surname;
        $this->email            = $email;
        $this->pass_hass        = $pass_hass;
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

        return true;
    }
}

class RdgTeacher implements IRDG {
    const _SELECT_BY = "select id, firstname, surname, email, hass_pass from tbl_teacher where `%s` = %s;";
    const _UPDATE_BY = "update tbl_teacher set firstname = '%s', surname = '%s', email = '%s', hass_pass = '%s' where id = %s;";
    const _DELETE_BY = "delete from tbl_teacher where id = %s;";
    const _INSERT_BY = "insert into tbl_teacher (firstname, surname, email, hass_pass) values ('%s', '%s', '%s', '%s');";

    #region StudentCheckIn\IRDG Members
    /**
     * Insert the object into the table
     *
     * @param TblTeacher $object
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
                DatabaseCMD::EscapeString($object->pass_hass)
            )
        );

        return !DatabaseCMD::LatestHasError();
    }

    /**
     * Summary of Update
     * Update a row in the table
     *
     * @param TblTeacher $object
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
     * @return TblTeacher|null
     */
    public static function Select($identifier) {
        return self::SelectBy('id', DatabaseCMD::EscapeString($identifier));
    }

    /**
     * Summary of ResultToObject
     * Converts a mysqli result to an entity class object
     *
     * @param mixed $input
     * @return TblTeacher
     */
    public static function ResultToObject($input) {
        if (is_null($input)) {
        	return null;
        }

        return new TblTeacher(intval($input['id']), $input['firstname'], $input['surname'],
            $input['email'], $input['hass_pass']);
    }
    #endregion

    /**
     * Summary of SelectBy
     * A generic select
     *
     * @param string $by
     * @param mixed $value
     *
     * @return TblTeacher
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
     * @return TblTeacher
     */
    public static function SelectByEmail($email) {
        return self::SelectBy('email', sprintf("'%s'", DatabaseCMD::EscapeString($email)));
    }

    /**
     * Summary of SelectByHassPass
     * @param string $hass_pass 
     * @return TblTeacher
     */
    public static function SelectByHassPass($hass_pass) {
        return self::SelectBy(
            'hass_pass', 
            sprintf("'%s'", DatabaseCMD::EscapeString($hass_pass))
        );
    }
}
