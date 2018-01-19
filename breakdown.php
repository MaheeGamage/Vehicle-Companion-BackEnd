<?php
        // Enabling error reporting
        error_reporting(-1);
        ini_set('display_errors', 'On');

        require_once __DIR__ . '/firebase/firebase.php';
        require_once __DIR__ . '/firebase/push.php';

        require_once 'include/DB_Functions.php';
        $db = new DB_Functions();

        // json response array
        $response = array("error" => FALSE, "firebase_error" => FALSE);

        if (isset($_POST['uid'])) {

            $firebase = new Firebase();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['team'] = 'India';
            $payload['score'] = '5.6';

            $uid = $_POST['uid'];
            $suser = $db->getAllSelectedUsers($uid);

            // notification title
            $title = "Breakdown";/*isset($_GET['title']) ? $_GET['title'] : '';*/
            
            // notification message
            $message = "Your friend has been breakdown";/*isset($_GET['message']) ? $_GET['message'] : '';*/
            
            // push type - single user / topic
            $push_type = "individual";/*isset($_GET['push_type']) ? $_GET['push_type'] : '';*/
            
            // whether to include to image or not
            $include_image = FALSE;/*isset($_GET['include_image']) ? TRUE : FALSE;*/

            if($suser != false) {
                $push->setTitle($title);
                $push->setMessage($message);
                if ($include_image) {
                    $push->setImage('http://api.androidhive.info/images/minion.jpg');
                } else {
                    $push->setImage('');
                }
                $push->setIsBackground(FALSE);
                $push->setPayload($payload);


                $json = '';
                $response = '';

                if ($push_type == 'topic') {
                    $json = $push->getPush();
                    $response = $firebase->sendToTopic('global', $json);
                } else if ($push_type == 'individual') {
                    $json = $push->getPush();
                    while ($row = $suser->fetch_assoc()) {
                        $regId = $db->getUserFirebaseIdFromId($row["id"]);  
                        $response = $firebase->send($regId, $json);
                    }
                }

                echo json_encode($json);
                echo "<\br>";
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