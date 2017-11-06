<?php
namespace StudentCheckIn;

abstract class ManageClasses {
    /**
     * Summary of CreateClass
     * Creates a new class and returns the id (id it fails then the result is false)
     *
     * @param string $class_name
     *
     * @return \boolean|integer
     */
    public static function CreateClass($class_name) {
        try {
            $ok = RdgClass::Insret(new TblClass(-1, $class_name));

            if (!$ok) {
            	return false;
            }

            $result = RdgClass::SelectByClass($class_name);

            if (!$result) {
                return false;
            }

            return $result->id;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
