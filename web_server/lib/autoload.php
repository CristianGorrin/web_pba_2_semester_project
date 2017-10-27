<?php
namespace StudentCheckIn;
class Autoload {
    protected static $classes = array(
        'StudentCheckIn\\ConfDatabase' => '/conf/database.php',
        'StudentCheckIn\\DatabaseCMD'  => '/database/database_cmd.php',
        'StudentCheckIn\\IEntity'      => '/database/i_entity.php',
        'StudentCheckIn\\IRDG'         => '/database/i_rdg.php',
        'StudentCheckIn\\TblMetadata'  => '/database/tbl_metadata.php',
        'StudentCheckIn\\RdgMetadata'  => '/database/tbl_metadata.php',
        'StudentCheckIn\\TblClass'     => '/database/tbl_class.php',
        'StudentCheckIn\\RdgClass'     => '/database/tbl_class.php',
        'StudentCheckIn\\RdgStudent'   => '/database/tbl_student.php',
        'StudentCheckIn\\TblStudent'   => '/database/tbl_student.php',
        'StudentCheckIn\\TblTeacher'   => '/database/tbl_teacher.php',
        'StudentCheckIn\\RdgTeacher'   => '/database/tbl_teacher.php',
        'StudentCheckIn\\TblSubject'   => '/database/tbl_subject.php',
        'StudentCheckIn\\RdgSubject'   => '/database/tbl_subject.php'
    );

    public static function Loaded($class) {
        if (isset(self::$classes[$class])) {
		    require __DIR__ . self::$classes[$class];
	    }
    }
}

spl_autoload_register('\\StudentCheckIn\\Autoload::Loaded');
