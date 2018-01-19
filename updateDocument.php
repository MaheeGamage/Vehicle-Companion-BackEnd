<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE, "lic" => FALSE, "ins" => FALSE);

if ((isset($_POST['license_expiry_date']) || isset($_POST['insurance_expiry_date'])) && isset($_POST['uid']) && isset($_POST['vid'])) {

    // receiving the post params
    $uid = $_POST['uid'];
    $vid = $_POST['vid'];

    if(isset($_POST['license_expiry_date'])){
        $license_expiry_date = $_POST['license_expiry_date'];
        $license = $db->storeDocument("license",$license_expiry_date,$uid,$vid);

        if ($license != false) {
        // service station(s) found
        $response['lic'] = TRUE; 
        $response['license']['error'] = FALSE;
        $response['license']['id'] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $license['id']);
        $response['license']['expiry_date'] = $license['expiry_date'];
        
        }else {
            // user is not found with the credentials
            $response['license']['error'] = TRUE;
            $response['license']['error_msg'] = "Erro while saving license data";
        }
    }

    if(isset($_POST['insurance_expiry_date'])){
        $insurance_expiry_date = $_POST['insurance_expiry_date'];
        $insurance = $db->storeDocument("insurance",$insurance_expiry_date,$uid,$vid);

         if ($insurance != false) {
        // service station(s) found
        $response['ins'] = TRUE; 
        $response['insurance']['error'] = FALSE;
        $response['insurance']['id'] = preg_replace('/"([^"]+)":\s*(\d+)/', '"\1": "\2"', $insurance['id']);
        $response['insurance']['expiry_date'] = $insurance['expiry_date'];
        
        }else {
            // user is not found with the credentials
            $response['insurance']['error'] = TRUE;
            $response['insurance']['error_msg'] = "Erro while saving insurance data";
        }
    }

    echo json_encode($response);

} else {
    // required post params is missing
    $response['error'] = TRUE;
    $response['error_msg'] = "Required parameters are missing!";
    echo json_encode($response);
}
?>