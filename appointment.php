<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['type']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['user_id']) && isset($_POST['branch_id'])) {

    // receiving the post params
    $type = $_POST['type'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $user_id = $_POST['user_id'];
    $branch_id = $_POST['branch_id'];

    // check if user is already existed with the same email
    if ($db->isAppointmentExisted($date, $time, $branch_id)) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "Appointment already existed";
        echo json_encode($response);
    } else {
        // create a new user
        $appointment = $db->storeAppointment($type, $date, $time, $user_id, $branch_id);
        if ($appointment) {
            $response["error"] = FALSE;
            $response["id"] = $appointment["id"];
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in booking!";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters are missing!";
    echo json_encode($response);
}
?>