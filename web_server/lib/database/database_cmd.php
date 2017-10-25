<?php
namespace StudentCheckIn;
class DatabaseCMD {
    protected static $destroyed;
    protected static $db_cmd;
    protected static $latest_error;

    /**
     * Summary of Connect
     * Creates a new connection to the database (based on values from ConfDatabase class)
     *
     * @param string $address
     * @param int $port
     * @param string $user
     * @param string $password
     * @param string $database
     */
    public static function NewConnection() {
        self::$db_cmd = \mysqli_connect(ConfDatabase::$address, ConfDatabase::$user,
            ConfDatabase::$password, ConfDatabase::$database, ConfDatabase::$port);
        self::$destroyed = false;
    }

    /**
     * Summary of Destroy
     * This will closed the connection to the database
     */
    public static function Destroy() {
        if (!self::$destroyed) {
            mysqli_close(self::$db_cmd);
            self::$destroyed = true;
        }
    }

    /**
     * Summary of ExecutedStatement
     * Executed a query and returns the result
     *
     * @param string $query
     * @return mixed
     */
    public static function ExecutedStatement($query) {
        self::$latest_error = '';

        $result = mysqli_query(self::$db_cmd, $query);

        self::$latest_error = mysqli_error(self::$db_cmd);
        
        return $result;
    }

    /**
     * Summary of LatestHasErrors
     * Test if there is an error the most recent call
     *
     * @return boolean
     */
    public static function LatestHasError() {
        return strlen(self::$latest_error) > 0;
    }

    /**
     * Summary of GetErrorMessage
     * Get the error message of the most recent call
     *
     * @return string
     */
    public static function GetErrorMessage() {
        return self::$latest_error;
    }

    /**
     * Summary of EscapeString
     * Escapes string for use in a sql statement
     *
     * @param string $input
     * @return string
     */
    public static function EscapeString($input) {
        return mysqli_real_escape_string(self::$db_cmd, $input);
    }
}

// This is the init
DatabaseCmd::NewConnection();
