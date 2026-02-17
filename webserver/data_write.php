<?php
session_start();

$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

// Simple authorization with nonce
if (empty($_SESSION['nonce']))
    $_SESSION['nonce'] = uniqid();

if (empty($_POST['s'])) {
    header('X-Custom-Auth: '.$_SESSION['nonce']);
    header("HTTP/1.1 401 Unauthorized");
    die("Unauthorized ");
}

if (empty($_POST['t']) || empty($_POST['p']) || empty($_POST['a']) || empty($_POST['h']) || empty($_POST['v']))
	die('invalid data');

$temperature = $_POST['t'];
$pressure = $_POST['p'];
$altitude = $_POST['a'];
$humidity = $_POST['h'];
$voltage = $_POST['v'];
$datetime = date('Y-m-d H:i:s');


// Create connection
try {
    $conn = new SQLite3('db/sensors.sqlite');
} catch (Throwable $e) {
    die("Can't connect to database: " . $e->getMessage());
}


// Enumerate sensors
$stmt = $conn->prepare("SELECT id, name, pin FROM sensors");
$result = $stmt->execute();
$sensors = [];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    array_push($sensors, $row);
}


// Signature check
if (!empty($_POST['s'])) {
    foreach ($sensors as $sensor) {
	    $string = $sensor['pin'].$_SESSION['nonce'].$temperature.$pressure.$altitude.$humidity.$voltage;
	    if ($_POST['s'] == md5($string))
		    $authorized = $sensor['id'];
	}
	if (empty($authorized)) {
        header("HTTP/1.1 401 Unauthorized");
        die('Invalid signature');
    }
}


$stmt = $conn->prepare("INSERT INTO data_".$authorized." (d, t, p, a, h, v) VALUES (:d, :t, :p, :a, :h, :v)");
$stmt->bindValue(':d', $datetime, SQLITE3_TEXT);
$stmt->bindValue(':t', $temperature, SQLITE3_TEXT);
$stmt->bindValue(':p', $pressure, SQLITE3_TEXT);
$stmt->bindValue(':a', $altitude, SQLITE3_TEXT);
$stmt->bindValue(':h', $humidity, SQLITE3_TEXT);
$stmt->bindValue(':v', $voltage, SQLITE3_TEXT);

$stmt->execute();

$stmt->close();
$conn->close();

echo "OK"
?>
