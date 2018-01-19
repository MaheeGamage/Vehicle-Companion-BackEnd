<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE, "firebase_error" => FALSE);

if (isset($_POST['email']) && isset($_POST['password'])) {

    // receiving the post params
    $email = $_POST['email'];
    $password = $_POST['password'];
    // $fid = !empty($fid) ? "'$fid'" : "NULL";
    $fid = $_POST['fid'];
    $uid = $db->getUserIdFromEmail($email);
    $suser = $db->getAllSelectedUsers($uid);

    $firebase = $db->storeFirebaseIdByEmail($email, $fid);

    if ($firebase) {
        $response["firebase_error"] = FALSE;
        $response["user"]["fid"] = $firebase["firebase_id"];
    } else {
        // user failed to store
        $response["firebase_error"] = TRUE;
        $response["firebase_error_msg"] = "Unknown error occurred";
    }

    // get the user by email and password
    $user = $db->getUserByEmailAndPassword($email, $password, $fid);
    //get user's vehicle details
    if($user["owner"] == TRUE){
        $vehicle = $db->getVehicleDetailsByUserId($user["id"]);
        $license = $db->getLicenseDetailsByUserId($user["id"]);
        $insurance = $db->getInsuranceDetailsByUserId($user["id"]);
    }

    if ($user != false) {
        // use is found
        $response["error"] = FALSE;
        
        $response["id"] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $user["id"]);
        $response["user"]["name"] = $user["name"];
        $response["user"]["email"] = $user["email"];
        $response["user"]["phone_no"] = $user["phone_no"];
        $response["user"]["owner"] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $user["owner"]);

        while ($row = $suser->fetch_assoc()) {
                $response["selected_user"][] = array( 'id' => preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $row["id"]), 'email' => $row["email"] );
            }

        if($user["owner"] == TRUE && $vehicle !== false && $license !== false && $insurance !== false){
            $response["error2"] = FALSE;

            $response["vehicle"]["id"] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $vehicle["id"]);
            $response["vehicle"]["model"] = $vehicle["name"];
            $response["vehicle"]["reg_no"] = $vehicle["registration_no"];

            $response["license"]["id"] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $license["id"]);
            $response["license"]["expiry_date"] = $license["expiry_date"];

            $response["insurance"]["id"] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $insurance["id"]);
            $response["insurance"]["expiry_date"] = $insurance["expiry_date"];
        }
        echo json_encode($response);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Login credentials are wrong. Please try again!";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>