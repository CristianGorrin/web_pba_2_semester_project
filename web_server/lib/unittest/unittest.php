<?php
/**
 * Unit testing
 *
 * Unit test library short summary.
 *  This is a unit test library for php 7
 *
 * Unit test library description.
 *  Go to https://github.com/CristianGorrin/php_unittest
 *
 * @version 1.0
 * @author Cristian C. Gorrin <cristian_gorrin@live.com>
 */
namespace ccg\unittesting;
if (!isset($defined)) {
    /**
     * Summary of Vars
     * All the variables
     */
    class Vars {
        public static $project_name = 'Semester project';
        public static $build        = '1.0.0';
        public static $report_level = 0;
    }

    /**
     * Summary of UnittestException
     * The error code 0 is just a generic error (it defaults to it)
     * The error codes from 100 to 199 are for the assert class
     */
    class UnittestException extends \Exception {
        /**
         * Summary of __construct
         * Give the exception a message for debugging and an error code
         *
         * @param string $message
         * @param integer $code
         */
        public function __construct($message = '', $code = 0) {
            parent::__construct($message, $code);
        }

        /**
         * Summary of ToString
         * Gets this exception as human readable string
         * Optional used print_offset to give left padding
         *
         * @param integer $print_offset
         * @return string
         */
        public function ToString($print_offset = 0) {
            $str_offset = '';
            for ($i = 0; $i < $print_offset; $i++) {
                $str_offset .= ' ';
            }

            self::jTraceEx($this);

            $result = '';
            foreach (explode(PHP_EOL, self::jTraceEx($this)) as $line) {
                $result .= sprintf('%s%s' . PHP_EOL, $str_offset, $line);
            }

            return $result;
        }

        /**
         * Summary of __toString
         * Implicit conversion this exception to a human readable string
         *
         * @return string
         */
        public function __toString() {
            return $this->ToString(0);
        }

        /**
         * jTraceEx() - provide a Java style exception trace
         * Ref: http://php.net/manual/en/exception.gettraceasstring.php
         *
         * @param \Exception $e
         * @param \Exception $seen  array passed to recursive calls to accumulate trace lines already seen
         *                          leave as NULL when calling this function
         *
         * @return array of strings, one entry per trace line
         */
        public static function jTraceEx($e, $seen = null) {
            $starter = $seen ? 'Caused by: ' : '';
            $result = array();
            if (!$seen) $seen = array();
            $trace  = $e->getTrace();
            $prev   = $e->getPrevious();
            $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
            $file = $e->getFile();
            $line = $e->getLine();
            while (true) {
                $current = "$file:$line";
                if (is_array($seen) && in_array($current, $seen)) {
                    $result[] = sprintf(' ... %d more', count($trace)+1);
                    break;
                }
                $result[] = sprintf(' at %s%s%s(%s%s%s)',
                                            count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
                                            count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
                                            count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
                                            $line === null ? $file : basename($file),
                                            $line === null ? '' : ':',
                                            $line === null ? '' : $line);
                if (is_array($seen))
                    $seen[] = "$file:$line";
                if (!count($trace))
                    break;
                $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
                $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
                array_shift($trace);
            }
            $result = join(PHP_EOL, $result);
            if ($prev)
                $result  .= PHP_EOL . self::jTraceEx($prev, $seen);

            return $result;
        }
    }

    class Assert {
        /**
         * Summary of Fail
         * This will fail the test without checking any conditions
         * Optional add a messages for info
         *
         * @param string $messages
         * @throws UnittestException
         */
        public static function Fail($messages) {
            throw new UnittestException($messages, 100);
        }

        /**
         * Summary of Inconclusive
         * The test can't be verified
         *
         * @param string $messages
         * @throws UnittestException
         */
        public static function Inconclusive($messages) {
            throw new UnittestException($messages, 101);
        }

        /**
         * Summary of AreEqual
         * Test if the value is as expected
         *
         * @param mixed $value
         * @param mixed $expected
         * @param string $messages
         * @throws UnittestException
         */
        public static function AreEqual($value, $expected, $messages = '') {
            if ($value !== $expected) {
                if ($messages != '') {
                    $messages = ': ' . $messages;
                }


                throw new UnittestException(
                    sprintf(
                        'The value is not as expected... "%s"[expected] is not equal to "%s"%s',
                        self::StrValue($expected),
                        self::StrValue($value),
                        $messages
                    ),
                    111
                );
            }
        }

        /**
         * Summary of AreNotEqual
         * Test if the value has a different from the other value
         *
         * @param mixed $value
         * @param mixed $not_allowed
         * @param string $messages
         * @throws UnittestException
         */
        public static function AreNotEqual($value, $not_allowed, $messages = '') {
            if ($value === $not_allowed) {
                if ($messages != '') {
                    $messages = ': ' . $messages;
                }

                throw new UnittestException(
                    sprintf(
                        'The value is not allowed... "%s"[not allowed] are equal to "%s"%s',
                        self::StrValue($not_allowed),
                        self::StrValue($value),
                        $messages
                    ),
                    112
                );
            }
        }

        /**
         * Summary of IsFalse
         * Test if the value is false
         *
         * @param mixed $value
         * @param string $messages
         * @throws UnittestException
         */
        public static function IsFalse($value, $messages = '') {
            if ($value !== false) {
                if ($messages != '') {
                    $messages = ': ' . $messages;
                }

                throw new UnittestException(
                    sprintf(
                        'The value is not false... "%s"%s',
                        self::StrValue($value),
                        $messages
                    ),
                    113
                );
            }
        }

        /**
         * Summary of IsTrue
         * Test if the value is true
         *
         * @param mixed $value
         * @param string $messages
         * @throws UnittestException
         */
        public static function IsTrue($value, $messages = '') {
            if ($value !== true) {
                if ($messages != '') {
                    $messages = ': ' . $messages;
                }

                throw new UnittestException(
                    sprintf(
                        'The value is not true... "%s"%s',
                        self::StrValue($value),
                        $messages
                    ),
                    114
                );
            }
        }

        /**
         * Summary of IsInstanceOfType
         * Test if an object is of specific type
         * Note: the expected type is string "\namespace\class"
         *
         * @param mixed $object
         * @param string $expected_type
         * @param string $messages
         * @throws UnittestException
         */
        public static function IsInstanceOfType($object, $expected_type, $messages = '') {
            if ($messages != '') {
                $messages = ': ' . $messages;
            }

            if (gettype($object) != 'object') {
                throw new UnittestException('The supplied value is not a object...', 115);
            } elseif (get_class($object) != $expected_type) {
                throw new UnittestException(
                    sprintf(
                        'The object is not of the expected type... "%s"[expected type] but the object is "%s"%s',
                        $expected_type,
                        get_class($object),
                        $messages
                    ),
                    116
                );
            }
        }

        /**
         * Summary of IsNull
         * Test if a value is null
         *
         * @param mixed $value
         * @param string $messages
         * @throws UnittestException
         */
        public static function IsNull($value, $messages = '') {
            if (!is_null($value)) {
                if ($messages != '') {
                    $messages = ': ' . $messages;
                }

                throw new UnittestException(
                    sprintf('The value is not null... "%s"%s', $value, $messages),
                    117
                );
            }
        }

        /**
         * Summary of StrValue
         * var_dumps a value and returns as a string
         *
         * @param mixed $value The value to be var_dump
         * @return string
         */
        protected static function StrValue($value) {
            ob_start();
            var_dump($value);
            $result = ob_get_clean();

            return trim(str_replace(PHP_EOL, ' ', $result));
        }
    }

    class Timer {
        protected $log_time;
        protected $accumulator;
        protected $pausede;

        //This is also used to reset the timer
        public function StartTimer() {
            $this->log_time    = microtime(true);
            $this->accumulator = 0;
            $this->pausede     = false;
        }

        //Get the elapsed time
        public function GetElapsedTime() {
            if (!$this->pausede) {
                return microtime(true) - $this->log_time + $this->accumulator;
            } else {
                return $this->accumulator;
            }
        }

        // Pause the timer
        public function PauseTimer() {
            $this->accumulator +=  microtime(true) - $this->log_time;
            $this->pausede = true;
        }

        // Resume the timer
        public function ResumeTimer() {
            $this->pausede  = false;
            $this->log_time = microtime(true);
        }
    }

    class Log {
        // The callback function to print the result
        const CALLBACK_RESULT = '\ccg\unittesting\Log::ConsolePrintResult';

        // The report level
        const INFO    = 0;
        const WARNING = 1;
        const ERROR   = 2;

        // The report levels as a string
        const STR_INFO   = 'info';
        const STR_WARING = 'warning';
        const STR_ERROR  = 'error';

        // The count of warnings, errors and test
        protected static $count_warning;
        protected static $count_error;
        protected static $count_test;

        protected static $init;          // [bool]  if the init function has been called
        protected static $report_level;  // [int]   the current reporting level
        protected static $elapsed_total; // [Timer] the total time used
        public static $elapsed_timer;    // [Timer] is for use in the tests

        protected static $test_results; /*  [array] the result of the test
         *  true is parsed and false is failed
         *  format: { 'test_name': string, 'result': bool }
         *
         *  the last value in this array is the current test
         */

        /**
         * Summary of Init
         * Initialize values
         *
         * @param integer $report_level
         * @return void
         */
        public static function Init($report_level) {
            if (isset(self::$init)) return;

            self::$report_level  = $report_level;
            self::$elapsed_timer = new Timer();
            self::$elapsed_total = new Timer();
            self::$elapsed_total->StartTimer();

            self::$count_error   = 0;
            self::$count_warning = 0;
            self::$count_test    = 0;

            self::$init = true;

            self::$test_results = array();
        }

        /**
         * Summary of IsInit
         * If the test has started
         *
         * @return boolean
         */
        public static function IsInit() {
            return isset(self::$init);
        }

        /**
         * Summary of ConsolePrint
         * Print a messages out
         * Type see report level
         *
         * @param string $messages
         * @param integer $type
         * @return void
         */
        public static function ConsolePrint($messages, $type = self::INFO) {
            $str_type = '';
            switch ($type) {
                case 0:
                    $str_type = self::STR_INFO;
                    break;
                case 1:
                    $str_type = self::STR_WARING;
                    self::$count_warning++;
                    break;
                case 2:
                    $str_type = self::STR_ERROR;
                    self::$count_error++;
                    break;
                default:
                    $str_type = 'default';
            }

            if ($type < self::$report_level) return;

            $temp = '[ ';
            $time = number_format(self::$elapsed_total->GetElapsedTime(), 4);

            for ($i = 0; $i < 12 - strlen($time); $i++) {
                $temp .= ' ';
            }

            $temp .= $time . sprintf(' ] <%s> ', $str_type) . $messages . PHP_EOL;

            echo $temp;
        }


        /**
         * Summary of ConsolePrintHeader
         * Prints a header to show a new test class has started
         *
         * @param string $header
         */
        public static function ConsolePrintHeader($header, $identifier) {
            array_push(
                self::$test_results,
                array(
                    'test_name'  => $header,
                    'result'     => true,
                    'identifier' => $identifier,
                    'test'       => array()
                )
            );

            self::ConsolePrint(sprintf('Stating: %s - %s', $identifier, $header));
        }

        /**
         * Summary of ConsolePrintTestName
         * Print the name of the test
         *
         * @param string $name
         */
        public static function ConsolePrintTestName($name) {
            self::$count_test++;
            self::ConsolePrint(sprintf('Function: %s', $name));

            $count = count(self::$test_results);
            if ($count > 0) {
                array_push(
                    self::$test_results[$count - 1]['test'],
                    array('name' => $name, 'result' => true, 'note' => '')
                );
            }
        }

        /**
         * Summary of ConsolePrintFooter
         * Prints the result information
         */
        public static function ConsolePrintResult() {
            $left_pading     = '  ';
            $left_pading_sub = '   ';

            echo 'Summary:' . PHP_EOL;
            if (count(self::$test_results) > 0) {
                $max_name_lenth = 0;
                foreach (self::$test_results as $result) {
                    $len = strlen($result['test_name']) + strlen($result['identifier']);

                    if ($len > $max_name_lenth) {
                        $max_name_lenth = $len;
                    }

                    foreach ($result['test'] as $sub_test) {
                        $len = strlen($sub_test['name']) + 3;

                        if ($len > $max_name_lenth) {
                            $max_name_lenth = $len;
                        }
                    }
                }

                foreach (self::$test_results as $result) {
                    $len = $max_name_lenth - strlen($result['test_name']) - strlen($result['identifier']);

                    $name_rigth_pading = '';
                    for ($i = 0; $i < $len; $i++) {
                        $name_rigth_pading .= ' ';
                    }

                    echo sprintf(
                        '%s%s: %s%s [%s]' . PHP_EOL,
                        $left_pading,
                        $result['identifier'],
                        $result['test_name'],
                        $name_rigth_pading,
                        self::GetStringOfResult($result['result'])
                    );

                    foreach ($result['test'] as $sub_test) {
                        $len = $max_name_lenth - strlen($sub_test['name']) - 1;

                        $name_rigth_pading = '';
                        for ($i = 0; $i < $len; $i++) {
                            $name_rigth_pading .= ' ';
                        }

                        echo sprintf(
                          '%s%s%s%s [%s] %s' . PHP_EOL,
                          $left_pading,
                          $left_pading_sub,
                          $sub_test['name'],
                          $name_rigth_pading,
                          self::GetStringOfResult($sub_test['result']),
                          $sub_test['note']
                        );
                    }

                    Log::ConsoleNewLine();
                }
            }

            echo PHP_EOL;

            $prased_tests      = 0;
            $failed_tests      = 0;
            $inconclusive_test = 0;
            foreach (self::$test_results as $result) {
                if (is_null($result)) {
                    $inconclusive_test++;
                } else if ($result['result']) {
                    $prased_tests++;
                } else {
                    $failed_tests++;
                }
            }
            echo sprintf('%sParsed test(s): %s' . PHP_EOL, $left_pading, $prased_tests);
            echo sprintf('%sFailed test(s): %s' . PHP_EOL, $left_pading, $failed_tests);
            if ($inconclusive_test > 0) {
                echo sprintf('%sInconclusive test(s): %s' . PHP_EOL, $left_pading, $inconclusive_test);
            }

            echo PHP_EOL;

            echo sprintf(
                '%sTime elapsed: %s ms' . PHP_EOL . '%sError(s): %s & warning(s): %s',
                $left_pading,
                number_format(self::$elapsed_total->GetElapsedTime() * 1000, 0, '.', ''),
                $left_pading,
                self::$count_error,
                self::$count_warning
            ) . PHP_EOL;
            echo sprintf('%sFunction(s) called: %s', $left_pading, self::$count_test), PHP_EOL;

            echo PHP_EOL;
            if (self::$count_error > 0) {
                echo 'The unit test completed with errors...';
            } else {
                echo 'The unit test completed with no errors!';
            }
            echo PHP_EOL;
        }

        /**
         * Summary of GetStringOfResult
         * Converters true to 'Parsed', false to 'Failed' and null to 'Inconclusive'
         *
         * @param bool|null $result
         * @return string
         */
        protected static function GetStringOfResult($result) {
            if (is_null($result)) {
                return 'Inconclusive';
            } else {
                return $result ? 'Parsed' : 'Failed';
            }
        }

        /**
         * Summary of ConsolePrintVarDump
         * This will print a var dump of a variable
         *
         * @param mixed $var
         */
        public static function ConsolePrintVarDump($variable, $message = '') {
            self::ConsolePrint(sprintf("Var dump: %s", $message));
            ob_start();
            var_dump($variable);
            $result = ob_get_clean();

            foreach (explode(PHP_EOL, $result) as $line) {
                if ($line == '') continue;
                echo sprintf('                 %s' . PHP_EOL, $line);
            }
        }

        /**
         * Summary of FailedCurrentTest
         * This will set the current test result to failed
         */
        public static function FailedCurrentTest($note = '') {
            $index = count(self::$test_results) - 1;
            if ($index < 0) return;

            $at = &self::$test_results[$index];

            $at['test'][count($at['test']) - 1]['result'] = false;
            $at['test'][count($at['test']) - 1]['note']   = $note;
            $at['result']                                 = false;
        }

        /**
         * Summary of InconclusiveCurrentTest
         * This will set the current test result to inconclusive
         *
         * @param string $note
         */
        public static function InconclusiveCurrentTest($note = '') {
            $index = count(self::$test_results) - 1;
            if ($index < 0) return;

            $at = & self::$test_results[$index];

            $at['test'][count($at['test']) - 1]['result'] = null;
            if ($at['result']) $at['result']              = null;
        }

        /**
         * Summary of ConsoleNewLine
         * Set the console curse on the next line
         */
        public static function ConsoleNewLine() {
            echo PHP_EOL;
        }
    }

    interface ITest {
        /**
         * Summary of GetIdentifier
         * The test name of this instances
         *
         * @return string
         */
        public function GetIdentifier();
    }

    class TestManager {
        const CALLBACK_EXECUTE_TEST = '\ccg\unittesting\TestManager::Execute';

        protected static $init;

        protected static $register_class_buffer;
        protected static $register_class_pointer;

        protected static $ignorere_function;

        public static function Init() {
            if (isset(self::$init)) return;
            self::$init = true;

            self::$register_class_buffer  = array();
            self::$register_class_pointer = 0;

            self::$ignorere_function = ['__construct', '__destruct', '__call', '__callStatic', '__get',
                '__set', '__isset', '__unset', '__sleep', '__wakeup', '__toString', '__invoke',
                '__set_state', '__clone', '__debugInfo', 'GetIdentifier'];
        }

        /**
         * Summary of Register
         * Register class for this unit test
         *
         * @param ITest $object
         */
        public static function Register(&$object) {
            array_push(
                self::$register_class_buffer,
                array(
                    'object' => $object,
                    'e' => new UnittestException()
                )
            );
        }

        /**
         * Summary of Execute
         * This will execute the register tests
         */
        public static function Execute() {
            // The references are only here to make the function more readable
            $pointer = &self::$register_class_pointer;
            $buffer  = &self::$register_class_buffer;

            $buffer_length = count($buffer);
            for ($pointer = 0; $pointer < $buffer_length; $pointer++) {
                $current = &$buffer[$pointer];

                if (!is_subclass_of($current['object'], '\ccg\unittesting\ITest')) {
                    Log::ConsolePrint(
                        "The object has to implement '\ccg\unittesting\ITest'...",
                        Log::ERROR
                    );
                    echo $current['e']->ToString(17);
                    Log::ConsolePrintVarDump($current['object'], 'value of the error');
                    continue;
                }

                $class = get_class($current['object']);
                Log::ConsolePrintHeader($class, $current['object']->GetIdentifier());

                $functions = array();
                foreach (get_class_methods($class) as $value) {
                    if (in_array($value, self::$ignorere_function)) continue;

                    $reflection = new \ReflectionMethod($class, $value);
                    if ($reflection->isStatic()) continue;

                    array_push($functions, $value);
                }

                if (count($functions) < 1) {
                    Log::ConsolePrint(
                        sprintf(
                            'The "%s - %s" has no test in it...',
                            $current['object']->GetIdentifier(),
                            $class
                        ),
                        Log::WARNING
                    );
                } else {
                    foreach ($functions as $test) {
                        Log::ConsolePrintTestName($test);

                        if (!method_exists($current['object'], $test)) {
                            $msg = sprintf(
                                "The method '%s' in '%s' doesn't exists...",
                                $test,
                                $class
                            );

                            Log::ConsolePrint($msg, Log::ERROR);
                            Log::FailedCurrentTest($msg);
                            continue;
                        }

                        try {
                            call_user_func(array($current['object'], $test));
                        }
                        catch (UnittestException $e) {
                            switch ($e->getCode()) {
                                case 101:
                                    self::ExceptionDefultHandler($e, Log::WARNING);
                                    break;
                                default:
                                    self::ExceptionDefultHandler($e, Log::ERROR);
                                    break;
                            }
                        }
                        catch (\Exception $e) {
                            $temp = explode(PHP_EOL, UnittestException::jTraceEx($e));
                            Log::FailedCurrentTest($e->getMessage());
                            Log::ConsolePrint(trim($temp[0]), Log::ERROR);

                            $length = count($temp);
                            $print = false;
                            for ($i = $length - 1; $i > 0; $i--) {
                                if ($temp[$i] == ' at call_user_func(Unknown Source)') {
                                    $print = true;
                                    continue;
                                }
                                if (!$print) continue;

                                echo '                 ' . $temp[$i] . PHP_EOL;
                            }
                        }
                    }
                }
            }

            Log::ConsoleNewLine();
        }

        /**
         * Summary of ExceptionDefultHandler
         *
         * @param UnittestException $e
         * @param int $type
         */
        protected static function ExceptionDefultHandler($e, $type) {
            $temp = explode(PHP_EOL, $e->ToString(17));

            if ($type == 1) {
                Log::InconclusiveCurrentTest($e->getMessage());
            } else {
                Log::FailedCurrentTest($e->getMessage());
            }

            Log::ConsolePrint(trim($temp[0]), $type);

            $length = count($temp);
            $print = false;
            for ($i = $length - 1; $i > 1; $i--) {
                if ($temp[$i] == '                  at call_user_func(Unknown Source)') {
                    $print = true;
                    continue;
                }
                if (!$print) continue;

                echo $temp[$i] . PHP_EOL;
            }

        }
    }
    TestManager::Init();

    class UnitTest {
        const MGES_HASNT_STARTED = "The test hasn't started yet: only call this function when the test has started.";
        const MGES_HAS_STARTED   = 'The test has started: this function can only be used before the test started.';

        // The report level
        const INFO    = 0;
        const WARNING = 1;
        const ERROR   = 2;

        /**
         * Summary of Log
         * Print a messages out
         *
         * @param string $messages The messages to be logged
         * @param int $type see report level UnitTest::[INFO, WARNING, ERROR]
         * @throws \Exception If the test hasn't started
         */
        public static function Log($messages, $type = self::INFO) {
            if (!Log::IsInit()) throw new \Exception(self::MGES_HASNT_STARTED);

            Log::ConsolePrint($messages, $type);
        }

        /**
         * Summary of NewLine
         * Prints a EOL
         *
         * @throws \Exception If the test hasn't started
         */
        public static function NewLine() {
            if (!Log::IsInit()) throw new \Exception(self::MGES_HASNT_STARTED);

            Log::ConsoleNewLine();
        }

        /**
         * Summary of VarDump
         * This will print a var dump of a variable
         *
         * @param mixed $variable The variable to be var dumped
         * @param string $message Some additional information
         * @throws \Exception If the test hasn't started
         */
        public static function VarDump($variable, $message = '') {
            if (!Log::IsInit()) throw new \Exception(self::MGES_HASNT_STARTED);

            Log::ConsolePrintVarDump($variable, $message);
        }

        /**
         * Summary of FailedCurrentTest
         * This will set the current test result to failed
         *
         * @param string $note Some text why it failed
         * @throws \Exception If the test hasn't started
         */
        public static function FailedCurrentTest($note = '') {
            if (!Log::IsInit()) throw new \Exception(self::MGES_HASNT_STARTED);

            Log::FailedCurrentTest($note);
        }

        /**
         * Summary of InconclusiveCurrentTest
         * This will set the current test result to inconclusive
         *
         * @param string $note Some text why it is inconclusive
         * @throws \Exception If the test hasn't started
         */
        public static function InconclusiveCurrentTest($note = '') {
            if (!Log::IsInit()) throw new \Exception(self::MGES_HASNT_STARTED);

            Log::InconclusiveCurrentTest($note);
        }

        /**
         * Summary of RegisterTest
         * Register object for this unit test
         *
         * @param ITest $object The object to be register
         * @throws \Exception If the test has started
         */
        public static function RegisterTest(&$object) {
            if (Log::IsInit()) throw new \Exception(self::MGES_HAS_STARTED);

            TestManager::Register($object);
        }

        /**
         * Summary of SetProjectName
         * Set the project name
         *
         * @param string $name The project name
         */
        public static function SetProjectName($name) {
            Vars::$project_name = $name;
        }

        /**
         * Summary of SetBulid
         * Set the project build name
         *
         * @param string $build The project build name
         */
        public static function SetBulid($build) {
            Vars::$build = $build;
        }
    }

    //Function to be called when the test is ready
    register_shutdown_function(function() {
        //This print the
        echo 'Unit test: ';

        $print_name  = Vars::$project_name != '';
        $print_bulid = Vars::$build != '';
        if ($print_name) {
            echo Vars::$project_name . ' ';
        }

        if ($print_bulid) {
            echo Vars::$build . ' ';
        }

        if ($print_name || $print_bulid) {
            echo '- ';
        }
        echo date('D M d o H:i') . PHP_EOL;

        Log::Init(Vars::$report_level);                     // Start Log
        call_user_func(TestManager::CALLBACK_EXECUTE_TEST); // Run tests
        call_user_func(Log::CALLBACK_RESULT);               // Print result
    });
}
$defined = true;
