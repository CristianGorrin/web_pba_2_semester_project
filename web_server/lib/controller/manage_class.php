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

    /**
     * Summary of GetAllClass
     * Get list of all class as json
     *
     * @return string
     */
    public static function GetAllClass() {
        $result = array();

        foreach (RdgClass::GetAll() as $value) {
            $result[$value->id] = $value->class;
        }

        return json_encode($result);
    }

    /**
     * Summary of AssignStudentsToClass
     * Assign students to a class and returns a array of all update student ids
     *
     * @param int $class_id The class id
     * @param int[] $student_ids The array of all student ids
     *
     * @return int[]
     */
    public static function AssignStudentsToClass($class_id, $student_ids) {
        $result = array();

        try {
            $temp = RdgClass::Select($class_id);

            if (is_null($temp)) {
                #region TODO remove - it only used for unit testing
                \ccg\unittesting\UnitTest::Log(
                    "The class doesn't exist...",
                    \ccg\unittesting\UnitTest::WARNING
                );
                #endregion
            	return $result;
            }

        } catch (\Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                "The class doesn't exist...",
                \ccg\unittesting\UnitTest::WARNING
            );
            #endregion
            return $result;
        }

        foreach ($student_ids as $value) {
        	try {
            	$student = RdgStudent::Select($value);

                if (!is_null($student)) {
                    $student->class = $class_id;

                    try {
                    	RdgStudent::Update($student);
                        array_push($result, $value);
                    } catch (\Exception $exception) {
                        #region TODO remove - it only used for unit testing
                        \ccg\unittesting\UnitTest::Log(
                            "Can't update student class",
                            \ccg\unittesting\UnitTest::WARNING
                        );
                        #endregion
                    }
                }
            } catch (\Exception $exception) {
                #region TODO remove - it only used for unit testing
                \ccg\unittesting\UnitTest::Log(
                    "The student doesn't exist... id:" . $value,
                    \ccg\unittesting\UnitTest::WARNING
                );
                #endregion
            }
        }

        return $result;
    }

    /**
     * Summary of AssingClassToSubject
     * Assign a class to a subject
     *
     * @param int $class_id
     * @param string $subject
     */
    public static function AssingClassToSubject($class_id, $subject) {
        $obj_subject = null;
        try {
        	$obj_subject = RdgSubject::SelectBySubject($subject);
        } catch (\Exception $exception) { }

        if (is_null($obj_subject)) {
        	try {
            	RdgSubject::Insret(new TblSubject(-1, $subject));
            } catch (\Exception $exception) {
                #region TODO remove - it only used for unit testing
                \ccg\unittesting\UnitTest::Log(
                    sprintf("Can't create the new subject: \"%s\"", $subject),
                    \ccg\unittesting\UnitTest::WARNING
                );
                #endregion
                return false;
            }
        }

        try {
        	$obj_subject = RdgSubject::SelectBySubject($subject);
        } catch (\Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                sprintf("Can't get the subject: \"%s\"", $subject),
                \ccg\unittesting\UnitTest::WARNING
            );
            #endregion
            return false;
        }

        if (is_null($obj_subject)) return false;

        try {
        	RdgSubjectClass::Insret(new TblSubjectClass(-1, $class_id, $obj_subject->id));
        } catch (\Exception $exception) {
            #region TODO remove - it only used for unit testing
            \ccg\unittesting\UnitTest::Log(
                "Can't assign the subject to the class",
                \ccg\unittesting\UnitTest::WARNING
            );
            #endregion
            return false;
        }

        return true;
    }
}
