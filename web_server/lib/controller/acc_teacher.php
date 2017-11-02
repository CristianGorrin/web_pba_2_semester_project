<?php
namespace StudentCheckIn;

abstract class AccTeacher {
    /**
     * Summary of Singup
     * Create a new account for a teacher
     *
     * @param string $firstname The name of the teacher
     * @param string $surname The surname of the teacher
     * @param string $email The teachers email
     * @param string $pass_word The password (not the hashed password - this function will do it before saving it to the database)
     *
     * @return boolean
     */
    public static function Singup($firstname, $surname, $email, $password) {
        $hass_pass = '';

        do {
            $hass_pass = password_hash($password, PASSWORD_BCRYPT);
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                sprintf('Hashing the password: "%s" => "%s"', $password, $hass_pass)
            );
            #endregion
        } while (!is_null(RdgTeacher::SelectByHassPass($hass_pass)));

        try {
            $new_acc = new TblTeacher(-1, $firstname, $surname, $email, $hass_pass);
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log('Creating a new teacher acc');
            \ccg\unittesting\UnitTest::VarDump($new_acc);
            #endregion
            RdgTeacher::Insret($new_acc);
        }
        catch (Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                'Failed to creating a new teacher acc',
                \ccg\unittesting\UnitTest::WARNING
            );
            #endregion
            return false;
        }
        #region TODO remove - it only used for unit testing
        \ccg\unittesting\UnitTest::Log(
            'Completed creating a new teacher acc'
        );
        #endregion
        return true;
    }

    /**
     * Summary of VerifyPassword
     * Verify the teachers password
     *
     * @param string $email The teachers email
     * @param string $password The teachers password
     * @param TblTeacher $acc If the acc is null it will just get the TblTeacher object based on the $email
     * @return boolean
     */
    public static function VerifyPassword($email, $password, $acc = null) {
        if (!is_null($acc)) {
            if ($acc->email != $email) {
            	return false;
            }
        } else {
        	try {
                $acc = RdgTeacher::SelectByEmail($email);
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

    /**
     * Summary of UpdatePassword
     * Update the password of a teacher acc
     *
     * @param string $old_password The password used now
     * @param string $new_password The new password
     * @param string $email The teachers email
     * @return boolean
     */
    public static function UpdatePassword($old_password, $new_password, $email) {
        $acc = null;
        try {
            $acc = RdgTeacher::SelectByEmail($email);
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
        } while (!is_null(RdgTeacher::SelectByHassPass($hass_pass)));

        $acc->pass_hass = $hass_pass;

        try {
            RdgTeacher::Update($acc);
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
}
