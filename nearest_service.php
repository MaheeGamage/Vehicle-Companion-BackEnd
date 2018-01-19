<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['lat']) && isset($_POST['lng'])) {

    // receiving the post params
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $type = $_POST['type'];

    // get the user by email and password
    $service = $db->getServiceStation($lat, $lng, $type);

    if ($service != false) {
        // service station(s) found
        $response["error"] = FALSE;

        while ($row = $service->fetch_assoc()) {
            $r[] = $row;
        }
        
        echo json_encode($r);

    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "There is no any Service station near you";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters latitude or longitude is missing!";
    echo json_encode($response);
}
?>