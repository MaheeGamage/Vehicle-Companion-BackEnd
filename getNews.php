<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

// get the user by email and password
$news = $db->getNews();

if (1/*$news != false*/) {
    // service station(s) found
    $response["error"] = FALSE;

    while ($row = $news->fetch_assoc()) {
        $r[] = $row;
    }
        
    echo json_encode($r);

} else {
    // user is not found with the credentials
    $response["error"] = TRUE;
    $response["error_msg"] = "There is no any Service station near you";
    echo json_encode($response);
}

?>