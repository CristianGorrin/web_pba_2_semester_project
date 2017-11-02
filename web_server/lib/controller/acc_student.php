<?php
namespace StudentCheckIn;

abstract class AccStudent {
    /**
     * Summary of Singup
     * @param string $first_name
     * @param string $surname
     * @param string $email
     * @param string $password
     * @param string $class_id
     * @return \boolean|string
     */
    public static function Singup($first_name, $surname, $email, $password, $class_id) {
        if (!is_null(RdgStudent::SelectByEmail($email))) {
        	return false;
        }

        $hass_pass = '';
        do {
            $hass_pass = password_hash($password, PASSWORD_BCRYPT);
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                sprintf('Hashing the password: "%s" => "%s"', $password, $hass_pass)
            );
            #endregion
        } while (!is_null(RdgStudent::SelectByPassHass($hass_pass)));

        $device_uuid = '';
        do {
        	$device_uuid = UUID::CreateV4();
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log('Device uuid: ' . $device_uuid);
            #endregion
        } while(!is_null(RdgStudent::SelectByDeviceUuid($device_uuid)));


        $obj = new TblStudent(-1, $first_name, $surname, $email, $hass_pass, false, $class_id,
            $device_uuid, '');

        #region TODO remove - it only used for unit testing
        \ccg\unittesting\Log::ConsolePrintVarDump($obj, 'The new sing up');
        #endregion
        try {
        	RdgStudent::Insret($obj);
        }
        catch (Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log('The insert of new student failed');
            #endregion
            return false;
        }

        return $device_uuid;
    }

    public static function VerifyPassword($email, $password, $acc = null) {
        if (!is_null($acc)) {
            if ($acc->email != $email) {
            	return false;
            }
        } else {
        	try {
                $acc = RdgStudent::SelectByEmail($email);
            }
            catch (Exception $exception) {
                #region TODO remove - it only used for unit testing
                \ccg\unittesting\UnitTest::Log(
                    sprintf("Can't find a acc with the email \"%s\"", $email),
                    \ccg\unittesting\UnitTest::WARNING
                );
                #endregion
                return false;
            }
        }

        return password_verify($password, $acc->pass_hass);
    }

    public static function UpdatePassword($old_password, $new_password, $email) {
        $acc = null;
        try {
            $acc = RdgStudent::SelectByEmail($email);
        }
        catch (Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                sprintf("Can't find a acc with the email \"%s\"", $email),
                \ccg\unittesting\UnitTest::WARNING
            );
            #endregion
            return false;
        }

        if (!self::VerifyPassword($email, $old_password, $acc)) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                "Can't verify old password...",
                \ccg\unittesting\UnitTest::WARNING
            );
            #endregion
        	return false;
        }

        $hass_pass = '';
        do {
            $hass_pass = password_hash($new_password, PASSWORD_BCRYPT);
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                sprintf('Hashing the password: "%s" => "%s"', $new_password, $hass_pass)
            );
            #endregion
        } while (!is_null(RdgStudent::SelectByPassHass($hass_pass)));

        $acc->pass_hass = $hass_pass;

        try {
            RdgStudent::Update($acc);
        }
        catch (Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                "Can't update the new hashed password...",
                \ccg\unittesting\UnitTest::WARNING
            );
            #endregion
            return false;
        }

        return true;
    }

    public static function ValidateAcc() {

    }

    public static function UpdateCacheStatistics() {

    }

    public static function UpdateAllCacheStatistics() {

    }

    public static function PairDevice() {

    }
}
