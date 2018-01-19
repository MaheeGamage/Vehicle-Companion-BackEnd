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
    $checkRow = $db->isSelectedUserExisted($sid, $uid);
    $user = $db->deleteSelectedUser($sid, $uid);

    if($checkRow != false){
        if ($user == false) {
            // use is found
            $response["error"] = FALSE;
            $response["msg"] = "User successfully deleted";
            $response["id"] = ('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $sid);
            
            echo json_encode($response);
        } 
        else {
            // user is not found with the credentials
            $response["error"] = TRUE;
            $response["id"] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $user["selected_user_id"]);
            $response["email"] = $email;
            $response["error_msg"] = "User deletion failed";

            echo json_encode($response);
        }
    }
    else{
        $response["error"] = TRUE;
        $response["error_msg"] = "User not exist";

        echo json_encode($response);
    }

} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>