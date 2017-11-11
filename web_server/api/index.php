<?php
namespace StudentCheckIn;
require '../lib/autoload.php';

header('Content-Type: application/json');

function HeaderBadRequest() {
    header($_SERVER["SERVER_PROTOCOL"] . ' 400 Bad Request');
    die();
}

function HeaderInternalServerError() {
    header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
    die();
}

// http://altorouter.com/usage/processing-requests.html
$router = new \AltoRouter();
$router->setBasePath(ConfAltoRouter::BASE_PATH_API);

// http://altorouter.com/usage/mapping-routes.html
$router->addRoutes(array(
    array('GET', '/update_cache_statistics', function() {
        if (!ConfGeneric::DEBUG_ENVIRONMENT) {
        	HeaderBadRequest();
        }

        require '../lib/cron/run_all.php';
        echo '{"result":true}';
    }),
    array('GET', '/ping', function() { echo '{"mgs":"pong"}'; }),
    array('POST', '/acc/students/singup', function() {
        // name, surname, email, password, class_id
        if (!isset($_POST["name"]) || !isset($_POST["surname"]) || !isset($_POST["email"]) ||
            !isset($_POST["password"]) || !isset($_POST["class_id"])) {
            HeaderBadRequest();
        }

        //TODO test if the user has privileges to complete this operation

        $result = AccStudent::Singup($_POST["name"], $_POST["surname"], $_POST["email"],
            $_POST["password"], intval($_POST["class_id"]));

        if ($result) {
            echo json_encode(array("result" => true, "device_uuid" => $result));
        } else {
            echo json_encode(array("result" => false));
        }
    }),
    array('POST', '/acc/students/login/[a:type]', function($type) {
        //email, password
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            HeaderBadRequest();
        }

        $result = array("result" => false);

        switch ($type) {
            case 'app':
                $acc = null;
                try {
                    $acc = RdgStudent::SelectByEmail($_POST['email']);
                } catch (\Exception $exception) {
                    HeaderInternalServerError();
                }

                if (!is_null($acc)) {
                    $result["result"] = AccStudent::VerifyPassword($_POST['email'],
                        $_POST['password']);
                } else {
                    $result["result"] = false;
                }

                if ($result["result"]) $result["device_uuid"] = AccStudent::PairDevice($acc->id);
                break;
        	default:
                HeaderBadRequest();
                return;
        }

        echo json_encode($result);
    }),
    array('POST', '/acc/students/update/password', function() {
        //old_password, new_password, email
        if (!isset($_POST['old_password']) || !isset($_POST['new_password']) ||
            !isset($_POST['email'])) {
        	HeaderBadRequest();
            return;
        }

        $result = AccStudent::UpdatePassword($_POST['old_password'], $_POST['new_password'],
            $_POST['email']);

        echo json_encode(array("result" => $result));
    }),
    array('POST', '/acc/students/logout/[a:type]', function($type) {
        // email, device_uuid
        if (!isset($_POST['email']) || !isset($_POST['device_uuid'])) {
            HeaderBadRequest();
        }

        switch ($type) {
            case 'app':
                if (AccStudent::IsDeviceOf($_POST['email'], $_POST['device_uuid'])) {
                    try {
                        AccStudent::PairDevice(RdgStudent::SelectByEmail($_POST['email'])->id);
                    } catch (\Exception $exception) {
                        HeaderInternalServerError();
                    }
                }
                break;
        	default:
                HeaderBadRequest();
        }
    }),
    array('POST', '/acc/students/info/device_uuid', function() {
        // email, password
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
        	HeaderBadRequest();
        }

        //TODO test if the user has privileges to complete this operation

        $result = array("result" => false);
        $acc = null;
        try {
            $acc = RdgStudent::SelectByEmail($_POST['email']);
        } catch (\Exception $exception) {
            HeaderInternalServerError();
        }


        if (!is_null($acc)) {
        	if (AccStudent::VerifyPassword($_POST['email'], $_POST['password'], $acc)) {
                $result['result'] = true;
                $result['device_uuid'] = $acc->device_uuid_v4;
            }
        }

        echo json_encode($result);
    }),
    array('GET', '/acc/students/info/statistics/[a:_1]-[a:_2]-[a:_3]-[a:_4]-[a:_5]',
        function($_1 = null, $_2 = null, $_3 = null, $_4 = null, $_5 = null) {
            if (is_null($_1) || is_null($_2) || is_null($_3) || is_null($_4) || is_null($_5)) {
                HeaderBadRequest();
            }

            $device_uuid = sprintf('%s-%s-%s-%s-%s', $_1, $_2, $_3, $_4, $_5);
            $acc         = null;

            try {
                $acc = RdgStudent::SelectByDeviceUuid($device_uuid);
            } catch (\Exception $exception) {
                HeaderInternalServerError();
            }


            if (!is_null($acc)) {
                echo AccStudent::GetCacheStatistics($acc->id);
            } else {
                echo '{"result": false}';
            }
        }),
    array('POST', '/acc/teacher/singup', function() {
        if (!isset($_POST['name']) || !isset($_POST['surname']) || !isset($_POST['email']) ||
            !isset($_POST['password'])) {
            HeaderBadRequest();
        }

        //TODO test if the user has privileges to complete this operation

        $result = AccTeacher::Singup(
            $_POST['name'], $_POST['surname'], $_POST['email'], $_POST['password']
        );

        echo json_encode(array('result' => $result));
    }),
    array('POST', '/acc/teacher/login', function() {
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            HeaderBadRequest();
        }

        $result = array("result" => false);
        $acc    = null;

        try {
        	$acc = RdgTeacher::SelectByEmail($_POST['email']);
        } catch (\Exception $exception) {
            HeaderInternalServerError();
        }

        if (!is_null($acc)) {
        	$result["result"] = AccTeacher::VerifyPassword(
                $_POST['email'], $_POST['password'], $acc
            );

            if ($result["result"]) {
                //TODO start session manager - use $acc
            }
        }

        echo json_encode($result);
    }),
    array('GET', '/acc/teacher/info/[i:id]', function($id = null) {
        if (is_null($id)) {
        	HeaderBadRequest();
        }

        //TODO test if the user has privileges to complete this operation

        $acc = null;

        try {
        	$acc = RdgTeacher::Select($id);
        } catch (Exception $exception) {
            HeaderInternalServerError();
        }

        if (is_null($acc)) {
        	echo '{"result": false}';
        } else {
            echo json_encode(array(
                "result"  => true,
                "id"      => $acc->id,
                "name"    => $acc->email,
                "surname" => $acc->surname,
                "email"   => $acc->email
            ));
        }
    }),
    array('POST', '/acc/teacher/update/password', function() {
        if (!isset($_POST['old_password']) || !isset($_POST['new_password']) ||
            !isset($_POST['email'])) {
        	HeaderBadRequest();
        }

        $result = array();

        $result["result"] = AccTeacher::UpdatePassword(
            $_POST['old_password'], $_POST['new_password'], $_POST['email']
        );

        echo json_encode($result);
    }),
    array('GET', '/class/list', function() {
        echo ManageClasses::GetAllClass();
    }),
    array('POST', '/class/create', function() {
        if (!isset($_POST['class_name'])) {
        	HeaderBadRequest();
        }

        //TODO test if the user has privileges to complete this operation

        echo json_encode(array("result" => ManageClasses::CreateClass($_POST['class_name'])));
    }),
    array('POST', '/class/assign/students', function() {
        // class_id, student_ids (json)
        if (!isset($_POST['class_id']) || !isset($_POST['student_ids'])) {
            HeaderBadRequest();
        }

        if (!is_numeric($_POST['class_id'])) {
            HeaderBadRequest();
        }

        //TODO test if the user has privileges to complete this operation

        $class_id    = intval($_POST['class_id']);
        $student_ids = json_decode($_POST['student_ids']);

        if (json_last_error() != JSON_ERROR_NONE) {
            HeaderBadRequest();
        }

        if (!is_array($student_ids)) {
        	HeaderBadRequest();
        }

        echo json_encode(ManageClasses::AssignStudentsToClass($class_id, $student_ids));
    }),
    array('POST', '/class/assing/subject', function() {
        // class_id, subject
        if (!isset($_POST["class_id"]) || !isset($_POST["subject"])) {
        	HeaderBadRequest();
        }

        if (!is_numeric($_POST["class_id"])) {
        	HeaderBadRequest();
        }

        //TODO test if the user has privileges to complete this operation

        $result = array(
            "result" => ManageClasses::AssingClassToSubject(
                intval($_POST["class_id"]), $_POST["subject"]
            )
        );

        echo json_encode($result);
    }),
    array('POST', '/class/info/log', function() {
        if (!isset($_POST['class_uuids'])) {
            HeaderBadRequest();
        }

        //TODO test if the user has privileges to complete this operation

        $result = json_decode($_POST['class_uuids']);
        if (json_last_error() != JSON_ERROR_NONE) {
            HeaderBadRequest();
        }

        if (!is_array($result)) {
        	HeaderBadRequest();
        }

        echo ManageClasses::GetClassLogInfo($result);
    })
));

// http://altorouter.com/usage/matching-requests.html
$match = $router->match();
if($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
