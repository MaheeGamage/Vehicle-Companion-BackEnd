<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['branch_id']) && isset($_POST['date'])) {

    // receiving the post params
    $id = $_POST['branch_id'];
    $date = $_POST['date'];

    // check selected date have free time slots
    $time = $db->isTimeAvailable($id, $date);

    if ($time != false) {

        $response["error"] = FALSE;
        $response["time"] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $time);
        
        echo json_encode($response);

    } else {
        // user is not found with the credentials
        $response["error"] = FALSE;
        $response["time"] = "0";
        echo json_encode($response);
    }

} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters are missing!";
    echo json_encode($response);
}
?>