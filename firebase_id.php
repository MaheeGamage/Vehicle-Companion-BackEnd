<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['uid']) && isset($_POST['fid'])) {

    // receiving the post params
    $uid = (is_numeric($_POST['uid']) ? (int)$_POST['uid'] : 0);
    $fid = $_POST['fid'];
    
    // create a new user
    $firebase = $db->storeFirebaseId($uid, $fid);
    if ($firebase) {
        $response["error"] = FALSE;
        $response["fid"] = $firebase["firebase_id"];
        echo json_encode($response);
    } else {
        // user failed to store
        $response["error"] = TRUE;
        $response["error_msg"] = "Unknown error occurred";
        echo json_encode($response);
    }
    
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters are missing!";
    echo json_encode($response);
}
?>