<?php
        // Enabling error reporting
        error_reporting(-1);
        ini_set('display_errors', 'On');

        require_once __DIR__ . '/firebase.php';
        require_once __DIR__ . '/push.php';

        $firebase = new Firebase();
        $push = new Push();

        // optional payload
        $payload = array();
        $payload['team'] = 'India';
        $payload['score'] = '5.6';

        // notification title
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        
        // notification message
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        
        // push type - single user / topic
        $push_type = isset($_POST['push_type']) ? $_POST['push_type'] : '';
        
        // whether to include to image or not
        $include_image = isset($_POST['include_image']) ? TRUE : FALSE;


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
            $regId = isset($_POST['regId']) ? $_POST['regId'] : '';
            $response = $firebase->send($regId, $json);
        }
        
        echo json_encode($json);
        echo "<\br>";
        echo json_encode($response);
        
?>