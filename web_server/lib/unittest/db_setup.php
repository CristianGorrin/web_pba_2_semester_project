<?php
echo 'Setting the database up - pleas wait...' . PHP_EOL;

$db_setup = file_get_contents('./db_setup.sql');
$db_data  = file_get_contents('./db_data.sql');

$db_cmd = mysqli_connect('127.0.0.1', 'dev', 'dev1234');

mysqli_multi_query($db_cmd, $db_setup . $db_data);

do {
	if ($result = mysqli_store_result($db_cmd)) {
    	mysqli_free_result($result);
    }

    $e = mysqli_error($db_cmd);

    if (strlen($e) > 0) {
        throw new \Exception($e);
    }
} while(mysqli_next_result($db_cmd));
mysqli_close($db_cmd);
unset($db_data);
unset($db_data);