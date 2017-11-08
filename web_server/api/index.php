<?php
namespace StudentCheckIn;
require '../lib/autoload.php';

// http://altorouter.com/usage/processing-requests.html
$router = new \AltoRouter();
$router->setBasePath(ConfAltoRouter::BASE_PATH_API);

// http://altorouter.com/usage/mapping-routes.html
$router->addRoutes(array(
      array('GET', '/', function($values) { var_dump('test');  }),
      array('GET', '/test/[a:la]_[i:tu]', function($values, $t) { var_dump($values); var_dump($t); })
));


// http://altorouter.com/usage/matching-requests.html
$match = $router->match();

if( $match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
