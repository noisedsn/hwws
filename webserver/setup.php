<?php
define('SESSION_LIFETIME', 7*24*60*60);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);
session_start();

include 'template/helpers.php';
$title = t('setup.title');


$firstSetup = false;
if (!isset($_SESSION['admin']))
    $_SESSION['admin'] = 0;

// DB connect or create
try {
    $db = new SQLite3('db/sensors.sqlite');
} catch (Throwable $e) {
    error(t('setup.cantcreatedb') ." Error message: " . $e->getMessage() . "\n");
}

$db->enableExceptions(true);

try {
    $stmt = $db->prepare('
        CREATE TABLE IF NOT EXISTS users (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          login TEXT NOT NULL,
          password TEXT NOT NULL,
          admin INTEGER DEFAULT NULL
        );
    ');
    $stmt->execute();
} catch (Exception $e) {
    error("Error creating schema: " . $e->getMessage());
}

// Create tables
try {
    $stmt = $db->prepare('
        CREATE TABLE IF NOT EXISTS sensors (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          name TEXT NOT NULL,
          pin INTEGER NOT NULL,
          elevation REAL DEFAULT NULL,
          private INTEGER DEFAULT NULL
        );
    ');
    $stmt->execute();
} catch (Exception $e) {
    error("Error creating schema: " . $e->getMessage());
}

// Enumerate users
try {
    $users = $userLogins = $userIds = [];
    $stmt = $db->prepare("SELECT * FROM users");
    $result = $stmt->execute();
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        array_push($users, $row);
        array_push($userLogins, $row['login']);
        array_push($userIds, $row['id']);
    }
    
    if (sizeof($users) == 0)
        $firstSetup = true;

    $htmlUsers = include 'template/table_users.php';
    
} catch (Throwable $e) {
    error("Can not list users: " . $e->getMessage() . "\n");
}

// Enumerate sensors
try {
    $sensors = $sensorNames = $sensorIds = $sensorPins = [];
    $stmt = $db->prepare("SELECT * FROM sensors");
    $result = $stmt->execute();
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        array_push($sensors, $row);
        array_push($sensorNames, $row['name']);
        array_push($sensorIds, $row['id']);
        array_push($sensorPins, $row['pin']);
    }

    $htmlSensors = include 'template/table_sensors.php';

} catch (Throwable $e) {
    error("Can not list sensors: " . $e->getMessage() . "\n");
}








$_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);


// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_POST['action']) {
        
        // Adding user
        case 'adduser':
            if (empty($_SESSION['admin']) && !$firstSetup)
                error("Administrator privileges required");
                
            if (in_array($_POST['login'], $userLogins)) {
                $db->close();
                error("User <strong>" . $_POST['login'] . "</strong> already exists");
            }
            
            try {
                $admin = (isset($_POST['admin'])) ? 1 : 0;
                $stmt = $db->prepare('INSERT INTO users (login, password, admin) VALUES (:login, :password, :admin);');
                $stmt->bindValue(':login', $_POST['login'], SQLITE3_TEXT);
                $stmt->bindValue(':password', password_hash($_POST['password'], PASSWORD_DEFAULT), SQLITE3_TEXT);
                $stmt->bindValue(':admin', $admin, SQLITE3_INTEGER);
                $result = $stmt->execute();
                $lastId = $db->lastInsertRowId();
            } catch (Exception $e) {
                $db->close();
                error("Error adding the user: " . $e->getMessage());
            }
            
            // Log in as new user, if not logged in already
            if (empty($_SESSION['loggedin'])) {
                $_SESSION['user_id'] = $lastId;
                $_SESSION['login'] = $_POST['login'];
                $_SESSION['loggedin'] = true;
                $_SESSION['admin'] = $admin;
            }
            success(t('setup.useradded'));
            $stmt->close();
            $db->close();

            break;



        // Adding sensor
        case 'addsensor':
            if (empty($_SESSION['admin']))
                error("Administrator privileges required");

            if (in_array($_POST['sensorname'], $sensorNames)) {
                $db->close();
                error("Sensor with the name <strong>" . $_POST['sensorname'] . "</strong> already exists");
            }
            
            do {
                $pin = rand (1000, 9999);
            } while (in_array($pin, $sensorPins)); // Keep looping if in_array is true

            // Create record for a new sensor
            $private = (isset($_POST['private'])) ? 1 : 0;
            $stmt = $db->prepare('INSERT INTO sensors (name, pin, elevation, private) VALUES (:name, :pin, :elevation, :private);');
            $stmt->bindValue(':name', $_POST['sensorname'], SQLITE3_TEXT);
            $stmt->bindValue(':pin', $pin, SQLITE3_INTEGER);
            $stmt->bindValue(':elevation', $_POST['elevation'], SQLITE3_FLOAT);
            $stmt->bindValue(':private', $private, SQLITE3_INTEGER);

            $result = $stmt->execute();

            $lastId = $db->lastInsertRowId();
            
            // Create table for a new sensor
            if ($lastId) {
                try {
                    $stmt = $db->prepare('
                        CREATE TABLE IF NOT EXISTS data_'.$lastId.' (
                            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                            d TEXT NOT NULL,
                            t REAL DEFAULT NULL,
                            p REAL DEFAULT NULL,
                            a REAL DEFAULT NULL,
                            h REAL DEFAULT NULL,
                            v REAL DEFAULT NULL
                        );
                    ');
                    $result = $stmt->execute();
                } catch (Exception $e) {
                    error("Error creating schema: " . $e->getMessage());
                }

                if ($result) {
                    ob_start();
                    include 'template/instruction.php';
                    $addContent = ob_get_clean();
                    success(t('setup.sensoradded'), $addContent);
                } else {
                    echo $db->lastErrorMsg();
                }

            }

            $stmt->close();
            $db->close();

            break;


        case 'deleteuser':
            if (empty($_SESSION['admin']))
                error("Administrator privileges required");

            if (!in_array($_POST['id'], $userIds)) {
                error("User with ID <strong>" . $_POST['id'] . "</strong> not found");
            }

            try {
                $stmt = $db->prepare('DELETE FROM users WHERE id = '.$_POST['id'].';');
                $stmt->execute();
            } catch (Exception $e) {
                $db->close();
                error("Error deletind the user: " . $e->getMessage());
            }
            
            if ($_SESSION['user_id'] == $_POST['id'])
                logout();

            success(t('setup.deleted'));
            
            $db->close();
            break;
        
        case 'deletesensor':
            if (empty($_SESSION['admin']))
                error("Administrator privileges required");

            if (!in_array($_POST['id'], $sensorIds)) {
                error("Sensor with ID <strong>" . $_POST['id'] . "</strong> not found");
            }

            try {
                $stmt = $db->prepare('DELETE FROM sensors WHERE id = '.$_POST['id'].';');
                $stmt->execute();
            } catch (Exception $e) {
                $db->close();
                error("Error deletind the sensor: " . $e->getMessage());
            }

            try {
                $stmt = $db->prepare('DROP TABLE IF EXISTS data_'.$_POST['id'].';');
                $stmt->execute();
            } catch (Exception $e) {
                $db->close();
                error("Error deletind sensor table: " . $e->getMessage());
            }

            success(t('setup.deleted'));
            
            $db->close();
            break;

        // Adding user
        case 'login':
            try {
                $stmt = $db->prepare("SELECT id, login, password, admin FROM users WHERE login = :login");
                $stmt->bindValue(':login', $_POST['login'], SQLITE3_TEXT);
                $result = $stmt->execute();
            } catch (Exception $e) {
                $db->close();
                error("Error: " . $e->getMessage());
            }
            $user = $result->fetchArray(SQLITE3_ASSOC);
            
            if (!$user || !password_verify($_POST['password'], $user['password']))
                error("Error: Wrong username or password!");

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['loggedin'] = true;
            $_SESSION['admin'] = $user['admin'];
            echo ('<script>window.location.href = "'.$_SERVER['REQUEST_URI'].'";</script>');

            break;
            
        case 'logout':
            logout();
            break;
    }

} else {
    
    if ($firstSetup)
        createuser($htmlUsers);

    if (empty($_SESSION['loggedin']))
        login();

}




// Template combinations
function success($message = '', $addContent = '') {
    $title = t('success');
    $htmlContent = '<p>' . $message . '</p>';
    $htmlContent .= $addContent;
    $htmlContent .= '<p><a href="javascript:history.back()"><span class="icon">'.icon('back').'</span>' . t('back') . '</a></p>';
    include 'template/setup.php';
    die();
}

function error($message = '') {
    $title = t('error');
    $htmlContent = "<p>" . $message . "</p>";
    $htmlContent .= '<p><a href="javascript:history.back()"><span class="icon">'.icon('back').'</span>' . t('back') . '</a></p>';
    include 'template/setup.php';
    die();
}

function createuser($htmlContent) {
    $title = t('setup.title');
    include 'template/setup.php';
    die();
}

function login() {
    $title = t('setup.log_in');
    $htmlContent = include 'template/form_login.php'; 
    include 'template/setup.php';
    die();
}

function logout() {
    session_unset();
    session_destroy();
    echo ('<script>window.location.href = "'.$_SERVER['REQUEST_URI'].'";</script>');
}

// Default template
$htmlContent = $htmlUsers . '<hr>' . $htmlSensors;
include 'template/setup.php'; 

?>

