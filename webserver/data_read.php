<?php
session_start();

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

$data = array(
    'd' => array(),
    't' => array(),
    'p' => array(),
    'a' => array(),
    'h' => array(),
    'v' => array(),
    'sensors' => array(),
    'sensor' => 1
);

$sensorIds = $resultsArray = array();


// Create connection
try {
    $conn = new SQLite3('db/sensors.sqlite', SQLITE3_OPEN_READONLY);
} catch (Throwable $e) {
    error("Can't connect to database: " . $e->getMessage());
}

// Enumerate sensors
try {
    $stmt = $conn->prepare("SELECT id, name, pin, elevation, private FROM sensors");
    $result = $stmt->execute();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        if (!empty($_SESSION['loggedin']) || !$row['private']) {
            $data['sensors'][$row['id']] = ['name' => $row['name'], 'elevation' => $row['elevation']];
            array_push($sensorIds, $row['id']);
        }
    }
    $stmt->close();
    
} catch (Throwable $e) {
    error("Can't enumerate sensors: " . $e->getMessage());
}

if (sizeof($data['sensors']) == 0)
    error("No sensors found or login required");

// Guess which sensor to show if not specified
if ( !empty($_GET['s']) && in_array($_GET['s'], $sensorIds) ) {
    $data['sensor'] = $_GET['s'];
} else {
    $data['sensor'] = $sensorIds[0];
}


if ( !empty($_GET['r']) ) {

    // Select data range if specified
    try {
        $stmt = $conn->prepare("
            SELECT * 
            FROM data_".$data['sensor']." 
            WHERE d >= DATETIME('now', 'localtime', '-".$_GET['r']." days') 
            ORDER BY d DESC;
        ");
        $result = $stmt->execute();
        while ($row = $result->fetchArray(SQLITE3_ASSOC))
            $resultsArray[] = $row;
        $stmt->close();
        
    } catch (Throwable $e) {
        error("Can't read data: " . $e->getMessage());
    }

} else {

    // Select daily mean values if range = 0 (all time)
    try {
        // last row for current conditions
        $stmt = $conn->prepare("SELECT * FROM data_".$data['sensor']." ORDER BY d DESC LIMIT 1;");
        $result = $stmt->execute();
        while ($row = $result->fetchArray(SQLITE3_ASSOC))
            $resultsArray[] = $row;
        $stmt->close();

        // daily mean values
        $stmt = $conn->prepare("
            SELECT
                DATE(d) AS d,
                round(avg(t), 2) as t, 
                round(avg(p), 2) as p, 
                round(avg(a), 2) as a, 
                round(avg(h), 2) as h, 
                round(avg(v), 2) as v
            FROM
                data_".$data['sensor']."
            GROUP BY
                DATE(d)
            ORDER BY
                d DESC;"
        );
        $result = $stmt->execute();
        while ($row = $result->fetchArray(SQLITE3_ASSOC))
            $resultsArray[] = $row;
        $stmt->close();
    
    } catch (Throwable $e) {
        error("Can't read data: " . $e->getMessage());
    }

}


foreach ($resultsArray as $row) {
    foreach ($row as $key => $value) {
        if (!isset($value)) $value = 0;
        if ($key == 'd') $value = strtotime($value)*1000;
        if ($key !== 'id') array_push($data[$key], $value);
    }
}

echo json_encode($data);
$conn->close();


function error($message = '') {
    http_response_code(503);
    error_log($message);
    die(1);
}

?>
