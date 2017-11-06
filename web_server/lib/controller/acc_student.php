<?php
namespace StudentCheckIn;

abstract class AccStudent {
    /**
     * Summary of Singup
     * Create a new student account
     *
     * @param string $first_name
     * @param string $surname
     * @param string $email
     * @param string $password
     * @param string $class_id
     *
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
        catch (\Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log('The insert of new student failed');
            #endregion
            return false;
        }

        return $device_uuid;
    }

    /**
     * Summary of VerifyPassword
     * Verify if password is valid
     *
     * @param string $email
     * @param string $password
     * @param string $acc
     *
     * @return boolean
     */
    public static function VerifyPassword($email, $password, $acc = null) {
        if (!is_null($acc)) {
            if ($acc->email != $email) {
            	return false;
            }
        } else {
        	try {
                $acc = RdgStudent::SelectByEmail($email);
            }
            catch (\Exception $exception) {
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
     * Update the password
     *
     * @param string $old_password
     * @param string $new_password
     * @param string $email
     * @return boolean
     */
    public static function UpdatePassword($old_password, $new_password, $email) {
        $acc = null;
        try {
            $acc = RdgStudent::SelectByEmail($email);
        }
        catch (\Exception $exception) {
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
        catch (\Exception $exception) {
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

    /**
     * Summary of ValidateAcc
     * Update account to valid stat
     *
     * @param string $email
     *
     * @return boolean
     */
    public static function ValidateAcc($email) {
        $acc = null;
        try {
            $acc = RdgStudent::SelectByEmail($email);
        }
        catch (\Exception $exception) {
            return false;
        }

        $acc->validate = true;

        try {
            return RdgStudent::Update($acc);
        }
        catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Summary of UpdateAllCacheStatistics
     * Update the cache statistics of all students
     */
    public static function UpdateAllCacheStatistics() {
        $update_cache = array(
            'tbl_class'     => array(),
            'tbl_subject'   => array(),
            'subject_class' => array(), //grouped by class
            'class_log'     => array()  //grouped by subject_class
        );

        foreach (RdgStudent::GetAllIds() as $value) {
            $temp_student = null;
            $temp_result = array(
                "subjects" => array(),
            );

            try {
                $temp_student = RdgStudent::Select($value);
            }
            catch (\Exception $exception) {
                continue;
            }

            if (is_null($temp_student)) continue;

            //TODO skip student if not in rolled

            if (!isset($update_cache['tbl_class'][$temp_student->class])) {
                $update_cache['tbl_class'][$temp_student->class] =
                    RdgClass::Select($temp_student->class);
            }

            if (!isset($update_cache['subject_class'][$temp_student->class])) {
                $update_cache['subject_class'][$temp_student->class] = array();
                foreach (RdgSubjectClass::GetAll($temp_student->class) as $value) {
                    $update_cache['subject_class'][$temp_student->class][$value->id] = $value;

                    if (!isset($update_cache['tbl_subject'][$value->subject])) {
                        $update_cache['tbl_subject'][$value->subject] =
                            RdgSubject::Select($value->subject);
                    }
                }
            }

            /** @var TblSubjectClass $subject_class */
            foreach ($update_cache['subject_class'][$temp_student->class] as $subject_class) {
                if (!isset($update_cache['class_log'][$subject_class->id])) {
                    $update_cache['class_log'][$subject_class->id] = array();
                    foreach (RdgClassLog::GetAll($subject_class->id) as $value) {
                        $update_cache['class_log'][$subject_class->id][$value->id] = $value;
                    }
                }

                /** @var TblClassLog $value */
                foreach ($update_cache['class_log'][$subject_class->id] as $value) {
                    $subject_name =
                        $update_cache['tbl_subject'][$subject_class->subject]->subject;

                    if (!isset($temp_result["subjects"][$subject_name])) {
                        $temp_result["subjects"][$subject_name] = array(
                            "_id"    => $update_cache['tbl_subject'][$subject_class->subject]->id,
                            "_stats" => array(
                                "total"    => 0,
                                "absences" => 0
                            )
                        );
                    }

                    $is_absenct = null == RdgRollCall::SelectByClassLogAndStudent(
                        $value->id,
                        $temp_student->id
                    );

                    $temp_result["subjects"][$subject_name]["_stats"]["total"] += $value->weight;
                    if ($is_absenct) {
                        $temp_result["subjects"][$subject_name]["_stats"]["absences"] +=
                            $value->weight;
                    }

                    $temp_result["subjects"][$subject_name][$value->class_uuid] = !$is_absenct;
                }
            }

            $temp_student->cache_statistics = json_encode($temp_result);
            RdgStudent::Update($temp_student);
        }

        $meta = RdgMetadata::Select('last_update_cache_statistics');
        $meta->value = time();
        RdgMetadata::Update($meta);
    }

    /**
     * Summary of GetCacheStatistics
     * Get the cache statistics
     *
     * @param int $student_id
     *
     * @return string|null
     */
    public static function GetCacheStatistics($student_id) {
        try {
            $obj = RdgStudent::Select($student_id);
        }
        catch (\Exception $exception) {
            return null;
        }

        if ($obj == null) {
        	return null;
        } else {
            return $obj->cache_statistics;
        }
    }

    /**
     * Summary of PairDevice
     * This will create a new device UUID for student account
     * The result is a UUID as string - but it can't is false
     *
     * @param int $acc_id
     *
     * @return \boolean|string
     */
    public static function PairDevice($acc_id) {
        $acc = null;
        try {
            $acc = RdgStudent::Select($acc_id);
        }
        catch (\Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                sprintf("Error in RdgStudent::Select(%s)", $acc_id)
            );
            #endregion
            return false;
        }

        if (is_null($acc)) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                "Can't faint the student account with the id of " . $acc_id
            );
            #endregion
        	return false;
        }

        $uuid = null;
        do {
        	$uuid = UUID::CreateV4();
        } while(!is_null(RdgStudent::SelectByDeviceUuid($uuid)));

        $acc->device_uuid_v4 = $uuid;

        try {
        	RdgStudent::Update($acc);
        }
        catch (\Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\Log::ConsolePrintVarDump(
                $acc,
                "Can't update the student account..."
            );
            #endregion
            return false;
        }

        return $uuid;
    }

    /**
     * Summary of IsDeviceOf
     * Test if the device UUID from the account - based on the eamil
     *
     * @param string $email
     * @param string $device_uuid
     * @return boolean
     */
    public static function IsDeviceOf($email, $device_uuid) {
        $acc = null;
        try {
        	$acc = RdgStudent::SelectByEmail($email);
        }
        catch (\Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                sprintf("RdgStudent::SelectByEmail(%s) failed", $email)
            );
            #endregion
            return false;
        }

        if (is_null($acc)) {
        	#region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                "Can't faint the student account with the email of " . $email
            );
            #endregion
            return false;
        }

        return $device_uuid == $acc->device_uuid_v4;
    }
}
