<?php

require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

//checking if 'type' is set and call appropriate function
if (isset($data['type'])) {
    switch ($data['type']) {
        

        case 'Login':
            $loginAPI = new UserLoginAPI($mysqli);
            echo $loginAPI->loginUser($data);
            break;

     
        default:
            apiResponse('error', 'Invalid request type', 400);
    }
} else {
    apiResponse('error', 'Invalid request', 400);
}

class UserLoginAPI {
    private $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function loginUser($data) {
        // Extract email and password from the POST data
        $email = $data['email'];
        $password = $data['password'];

        // Validate email and password
        if (empty($email) || empty($password)) {
            return $this->response('error', 'Email and password are required', 400);
        }

        // Check if the email exists in the database
        if ($this->emailExists($email)) {
            // Fetch the user data from the database
            $user = $this->getUserByEmail($email);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, return the API key
                return $this->response('success', 'User logged in successfully', 200, array('apikey' => $user['api_key'], 'userName' => $user['name']));
            } else {
                // Password is incorrect
                return $this->response('error', 'Invalid password', 401);
            }
        } else {
            // Email does not exist in the database
            return $this->response('error', 'Email not found', 404);
        }
    }

    // Method to retrieve user from the database by email
    private function getUserByEmail($email) {
        $stmt = $this->mysqli->prepare("SELECT * FROM User_Information WHERE email = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Method to check if email exists in the database
    private function emailExists($email) {
        $stmt = $this->mysqli->prepare("SELECT id FROM User_Information WHERE email = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Response function to return JSON data
    private function response($status, $message, $code, $data = null) {
        header("Content-Type: application/json");
        http_response_code($code);
        $response = array(
            'status' => $status,
            'message' => $message,
            'timestamp' => time(),
            'data' => $data
        );
        return json_encode($response);
    }
}


?>