<?php

class DB_Functions {

    private $conn;

    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {
        
    }

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password, $phone_no) {

        $stmt = $this->conn->prepare("INSERT INTO user( name, email, password, phone_no) VALUES( ?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $phone_no);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    public function storeAppointment($type, $date, $time, $user_id, $branch_id) {

        $stmt = $this->conn->prepare("INSERT INTO appointment( type, date, time, user_id, branch_id) VALUES( ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $type, $date, $time, $user_id, $branch_id);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM appointment WHERE date = ? AND time = ? AND branch_id = ?");
            $stmt->bind_param("ssi", $date, $time, $branch_id);
            $stmt->execute();
            $appointment = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $appointment;
        } else {
            return false;
        }
    }

    public function storeFirebaseId($uid, $fid) {

        $stmt = $this->conn->prepare("UPDATE user SET firebase_id=? WHERE id=?");
        $stmt->bind_param("si", $fid, $uid);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM user WHERE firebase_id = ?");
            $stmt->bind_param("s", $fid);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    public function storeFirebaseIdByEmail($email, $fid) {

        $stmt = $this->conn->prepare("UPDATE user SET firebase_id=? WHERE email=?");
        $stmt->bind_param("ss", $fid, $email);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM user WHERE firebase_id = ?");
            $stmt->bind_param("s", $fid);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    public function storeSelectedUser($sid , $uid) {

        $stmt = $this->conn->prepare("INSERT INTO selected_user( user_id, selected_user_id) VALUES( ?, ?)");
        $stmt->bind_param("ii", $uid, $sid);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM selected_user WHERE user_id = ? AND selected_user_id = ?");
            $stmt->bind_param("ii", $uid, $sid);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    public function storeDocument($type , $expiry_date , $user_id , $vehicle_id) {

        $stmt = $this->conn->prepare("INSERT INTO document( type, expiry_date, user_id, vehicle_id) VALUES( ?, ?, ?, ?) ON DUPLICATE KEY UPDATE type = ?, expiry_date = ?, user_id = ?, vehicle_id = ?");
        $stmt->bind_param("ssiissii", $type, $expiry_date, $user_id, $vehicle_id,$type, $expiry_date, $user_id, $vehicle_id);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM document WHERE vehicle_id = ? AND type = ?");
            $stmt->bind_param("is", $vehicle_id, $type);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }



    public function deleteSelectedUser($sid , $uid) {

        $stmt = $this->conn->prepare("DELETE FROM selected_user WHERE user_id = ? AND selected_user_id = ?");
        $stmt->bind_param("ii", $uid, $sid);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM selected_user WHERE user_id = ? AND selected_user_id = ?");
            $stmt->bind_param("ii", $uid, $sid);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    /**
     * Get user by email and password
     */

    public function getUserByEmailAndPassword($email, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // verifying user password
            $right_password = $user['password'];
            // check for password equality
            if ($password == $right_password) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }

    public function getVehicleDetailsByUserId($id)
    {
        $stmt = $this->conn->prepare("SELECT v.id, vt.name, vt.price, v.registration_no FROM vehicle v, vehicle_type vt WHERE v.type_id = vt.id and v.id = ?");

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $vehicle = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $vehicle;
            
        } else {
            return NULL;
        }
    }

    public function getLicenseDetailsByUserId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM `document` WHERE user_id = ? AND type = 'License'");

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $license = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $license;
            
        } else {
            return NULL;
        }
    }

    public function getInsuranceDetailsByUserId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM `document` WHERE user_id = ? AND type = 'Insurance'");

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $insurance = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $insurance;
            
        } else {
            return NULL;
        }
    }


    /**
     * Check user is existed or not
     */

    public function isUserExisted($email) {
        
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    public function isSelectedUserExisted($sid , $uid) {

        $stmt = $this->conn->prepare("SELECT * FROM selected_user WHERE user_id = ? AND selected_user_id = ?");
        $stmt->bind_param("ii", $uid, $sid);

        if ($stmt->execute()) {
            $id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $id;
            
        } else {
            return NULL;
        }

    }

    public function isAppointmentExisted($date, $time, $branch_id) {
        
        $stmt = $this->conn->prepare("SELECT * FROM appointment WHERE date = ? AND time = ? AND branch_id = ?");
        
        $stmt->bind_param("ssi", $date, $time, $branch_id);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    public function getServiceStation($lat,$lng,$type) {

        if($type == "spare")
            $stmt = $this->conn->prepare("SELECT *, (3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos( radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance FROM branch WHERE type = 'spare' OR type = 'service,spare' HAVING distance < 10 ORDER BY distance LIMIT 0 , 10");
        else if($type == "service")
            $stmt = $this->conn->prepare("SELECT *, (3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos( radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance FROM branch WHERE type = 'service' OR type = 'service,spare' HAVING distance < 10 ORDER BY distance LIMIT 0 , 10");
        else
            $stmt = $this->conn->prepare("SELECT *, (3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos( radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance FROM branch HAVING distance < 10 ORDER BY distance LIMIT 0 , 10");

        $stmt->bind_param("sss", $lat, $lng, $lat);

        if ($stmt->execute()) {
            $service = $stmt->get_result();

            $stmt->close();
            return $service;
            
        } else {
            return NULL;
        }

    }

    public function getNews() {

        $stmt = $this->conn->prepare("SELECT * FROM offer");

        if ($stmt->execute()) {
            $news = $stmt->get_result();
            // echo json_encode($stmt);

            $stmt->close();
            return $news;
            
        } else {
            return NULL;
        }

    }

    public function getVideo() {

        $stmt = $this->conn->prepare("SELECT * FROM video_tutorial");

        if ($stmt->execute()) {
            $news = $stmt->get_result();
            // echo json_encode($stmt);

            $stmt->close();
            return $news;
            
        } else {
            return NULL;
        }

    }

    public function getUserIdFromEmail($email) {

        $stmt = $this->conn->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $id["id"];
            
        } else {
            return NULL;
        }

    }

    public function getUserFirebaseIdFromId($uid) {

        $stmt = $this->conn->prepare("SELECT firebase_id FROM user WHERE id = ?");
        $stmt->bind_param("i", $uid);

        if ($stmt->execute()) {
            $id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $id["firebase_id"];
            
        } else {
            return NULL;
        }

    }

    public function getAllSelectedUsers($uid) {

        $stmt = $this->conn->prepare("SELECT U.id, U.email FROM `selected_user` as SU, user as U WHERE U.id = SU.selected_user_id AND user_id = ?");
        $stmt->bind_param("i", $uid);

        if ($stmt->execute()) {
            $user = $stmt->get_result();
            $stmt->close();
            
            return $user;
            
        } else {
            return NULL;
        }

    }

    public function isTimeAvailable($id, $date)
    {
        $reserved = 0;
        $stmt = $this->conn->prepare("SELECT time FROM `appointment` WHERE branch_id = ? AND date = ? ORDER BY time");

        $stmt->bind_param("is", $id, $date);

        if ($stmt->execute()) {
            $time = $stmt->get_result();

            while ($row = $time->fetch_assoc()) {
                $r[] = $row;
            }

            if(isset($r[0]["time"])){

                if($r[0]["time"] == '8am'){
                    $reserved =  1;
                }elseif ($r[0]["time"] == '12pm') {
                    $reserved =  2;
                }
                else {
                    $reserved =  0;
                }
            }else{
                $reserved =  0;
            }

            if(isset($r[1]["time"])){

                if ($r[1]["time"] == '12pm') {
                    if($reserved==1){
                        $reserved = 3;
                    }
                    else{
                        $reserved = 2;
                    }
                        
                }     
            }
            
            $stmt->close();
            return $reserved;
            
        } else {
            return NULL;
        }

    }



    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

}

?>