<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['uid']) && isset($_POST['email'])) {

    // receiving the post params
    $email = $_POST['email'];
    $uid = $_POST['uid'];

    // get the user by email and password
    $sid = $db->getUserIdFromEmail($email);
    $user = $db->storeSelectedUser($sid, $uid);

    if ($user != false) {
        // use is found
        $response["error"] = FALSE;
        $response["id"] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $user["selected_user_id"]);
        $response["email"] = $email;
        
        echo json_encode($response);
    } 
    else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "This User already selected OR There is no user like this";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>