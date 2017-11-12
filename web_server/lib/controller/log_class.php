<?php
namespace StudentCheckIn;

abstract class LogClass {
    /**
     * Summary of CreateClassLog
     * Create a new entry (class log)
     *
     * @param int $subject_class_id The id from tbl_subject_class
     * @param int $teacher_id The teacher account id from tbl_teacher
     * @param int $weight The class weight in the system
     * @param int $time The time of the class in Unixtime
     *
     * @return string|boolean
     */
    public static function CreateClassLog($subject_class_id, $teacher_id, $weight, $time = null) {
        if (is_null($time)) {
        	$time = time();
        }

        $uuid = null;
        do {
            $uuid = UUID::CreateV4();
        } while(RdgClassLog::SelectByClassUuid($uuid));

        try {
            RdgClassLog::Insret(
                new TblClassLog(-1, $uuid, $subject_class_id, $teacher_id, $time, $weight)
            );
        } catch (\Exception $exception) {
            return false;
        }

        return $uuid;
    }

    /**
     * Summary of RollCall
     * Register participation
     *
     * @param string $class_log_uuid
     * @param int $student_id
     * @param string $latiude
     * @param string $longitude
     *
     * @return boolean
     */
    public static function RollCall($class_log_uuid, $student_id, $latiude, $longitude) {
        $class_log = RdgClassLog::SelectByClassUuid($class_log_uuid);

        if (is_null($class_log)) {
        	return false;
        }

        try {
            if (!is_null(RdgRollCall::SelectByClassLogAndStudent($class_log->id, $student_id))) {
                return true;
            }

        	RdgRollCall::Insret(
                new TblRollCall(-1, $class_log->id, $student_id, $latiude, $longitude)
            );
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * Summary of XorMask
     * Xor the string and key
     *
     * @param string $string
     * @param string $key
     *
     * @return string
     */
    protected static function XorMask($string, $key) {
        $key_length    = strlen($key);
        $string_length = strlen($string);
        $result        = '';

        $result .= $string[0] ^ $key[0];
        for ($i = 1; $i < $string_length; $i++) {
            $result .= $string[$i] ^ $key[$i % $key_length];
        }

        return $result;
    }

    /**
     * Summary of CreateQrcodes
     * Create qr-codes - if the uuid doesn't give a class log the result is false
     * else the result is a json string
     *
     * @param int $amount How many intervals to be created
     * @param string $class_log_uuid The class log uuid
     *
     * @return boolean|string
     */
    public static function CreateQrcodes($amount, $class_log_uuid) {
        $log = RdgClassLog::SelectByClassUuid($class_log_uuid);
        if (is_null($log)) return false;

        $result = array(
            "uuid"  => $class_log_uuid,
            "start" => intval(time() / ConfGeneric::CODE_UPDATE_INTERVAL),
            "codes" => array()
        );

        for ($i = 0; $i < $amount; $i++) {
            $hash = hash(
                "sha256",
                $log->unix_time . self::XorMask(
                    ConfGeneric::SERVER_SECRET,
                    $class_log_uuid . intval(time() / ConfGeneric::CODE_UPDATE_INTERVAL + $i)
                )
            );

            $result["codes"][$i] = $hash;
        }

        return json_encode($result);
    }

    /**
     * Summary of ValidateQrcode
     * Validate the qr-code
     *
     * @param string $qrcode
     *
     * @return boolean
     */
    public static function ValidateQrcode($qrcode) {
        $now     = time() / ConfGeneric::CODE_UPDATE_INTERVAL;
        $at      = intval($now);
        $use_pre = $now - $at < ConfGeneric::CODE_UPDATE_OVERLAB;

        $class_log = RdgClassLog::SelectByClassUuid(substr($qrcode, 0, 36));
        $qr_hass   = substr($qrcode, 37);

        if (is_null($class_log)) return false;

        $hass = hash(
            "sha256",
            $class_log->unix_time . self::XorMask(
                ConfGeneric::SERVER_SECRET, $class_log->class_uuid . $at
            )
        );

        if ($hass == $qr_hass) {
            return true;
        }


        if ($use_pre) {
            $at--;
            $hass = hash(
                "sha256",
                $class_log->unix_time . self::XorMask(
                    ConfGeneric::SERVER_SECRET, $class_log->class_uuid . $at
                )
            );

            if ($hass == $qr_hass) {
                return true;
            }
        }

        return false;
    }
}
