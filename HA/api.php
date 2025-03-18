<?php
//including config
require_once 'config.php';

//user registraion class
class UserRegistrationAPI {
   
    private $mysqli;//trying  to establish database connection

    //db constructor
    public function __construct($db) {
        $this->mysqli = $db;
    }
    
    public function registerUser($data) {
        //extracting data from json post body
        $name = $data['name'];
        $surname = $data['surname'];
        $email = $data['email'];
        $password = $data['password'];
    
        //input validation
        if (empty($name)) 
        {
            return $this->response('error', 'ERROR:Name field hs been left empty', 400);
        }
        if( empty($surname))
        {
            return $this->response('error', 'ERROR:Surname field hs been left empty', 400);
        }
        if( empty($email))
        {
            return $this->response('error', 'ERROR:Email field hs been left empty', 400);
        }
        if(empty($password))
        {
            return $this->response('error', 'ERROR:Password field hs been left empty', 400);
        }


    
    
        //checking email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response('error', 'ERROR:Invalid email format', 400);
        }
    
        //checking password format,8 characterss,one special,oone lowercase,one Uppercase
        if (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*\W).{8,}$/', $password)) {
            return $this->response('error', 'ERROR:Password requirements not met,try again', 400);
        }
    
        //checking if email iss being used by another user
        if ($this->emailExists($email)) {
            return $this->response('error', 'ERROR:Email address is registered to another user', 400);
        }
    
        //hash password
        $hashed_password = $this->hashPassword($password);
    
        //generate api key
        $api_key = $this->generateApiKey();
    
        //inserting new user to api
        $sql = "INSERT INTO User_Information (name, surname, email, password, api_key) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
    
        if (!$stmt) {
            return $this->response('error', 'Failed to prepare statement: ' . $this->mysqli->error, 500);
        }
    
        $stmt->bind_param("sssss", $name, $surname, $email, $hashed_password, $api_key);
    
        if ($stmt->execute()) {
            return $this->response('success', 'User registered successfully', 200, array('apikey' => $api_key));
        } else {
            return $this->response('error', 'User registration failed: ' . $stmt->error, 500);
        }
    }
    
    //checking if user exists
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
    
    //hashing password
    private function hashPassword($password) {
        $options = [
            'cost' => 12,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    //function to generate apikey
    private function generateApiKey() {
        
        $length = 15;
        $key = bin2hex(random_bytes($length));
        return $key;
    }

    //response function to return JSON data
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

function apiResponse($status, $message, $code, $data = null) {
    $response = array(
        'status' => $status,
        'message' => $message,
        'timestamp' => time(),
        'data' => $data
    );
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

//creating instance of UserRegistrationAPI
$userRegistrationAPI = new UserRegistrationAPI($mysqli);

//this gets post data
$data = json_decode(file_get_contents('php://input'), true);

//checking if 'type' is set and call appropriate function
if (isset($data['type'])) {
    switch ($data['type']) {
        case 'Register':
            echo $userRegistrationAPI->registerUser($data);
            break;

        case 'Login':
            $loginAPI = new UserLoginAPI($mysqli);
            echo $loginAPI->loginUser($data);
            break;

        case 'AUCTIONS':
            $listingAPI = new ListingAPI($mysqli);
            echo $listingAPI->AUCTIONS($data);
            break;

        case 'CreateAuction':
            $createAuctionAPI = new CreateAuctionAPI($mysqli);
            echo $createAuctionAPI->createAuction($data);
            break;    
        
        case 'GetAuction':
            $getAuctionAPI = new GetAuctionAPI($mysqli);
            echo $getAuctionAPI->getAuction($data);
            break;    
            
        case 'UpdateAuction':
            $updateAuctionAPI = new UpdateAuctionAPI($mysqli);
            echo $updateAuctionAPI->updateAuction($data);
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

//////////////////////AUCTION STUFF//////////////////////////////////////////////

class CreateAuctionAPI {
    private $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function createAuction($data) {
        // Extract data from the request body
        $auction_name = $data['auction_name'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $listing_details = json_encode($data['listing_details']);
        $highest_bid = isset($data['highest_bid']) ? $data['highest_bid'] : null;
        $state = 'Waiting'; // Assuming 'Waiting' state for newly created auctions
        $buyer_id = null;  // No buyer initially
      
       // $auction_id = $this->generateAuctionID();

       
        // Input validation
        if (empty($auction_name) || empty($start_date) || empty($end_date) || empty($listing_details)) {
            return $this->response('error', 'All fields are required', 400);
        }

        // Insert the auction into the database
        $sql = "INSERT INTO auctions(AuctionID,auction_name, start_date, end_date, listing_details, highest_bid, state, buyer_id) VALUES (?, ?, ?, ?, ?,?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);

        if (!$stmt) {
            return $this->response('error', 'Failed to prepare statement: ' . $this->mysqli->error, 500);
        }

        // Bind parameters, ensuring nullability of highest_bid and buyer_id
        if ($highest_bid === null && $buyer_id === null) {
            $stmt->bind_param("ssssssss",$auction_id, $auction_name, $start_date, $end_date, $listing_details, $highest_bid, $state, $buyer_id);
        } elseif ($highest_bid === null) {
            $stmt->bind_param("sssssssi", $auction_id,$auction_name, $start_date, $end_date, $listing_details, $highest_bid, $state, $buyer_id);
        } else {
            $stmt->bind_param("sssssdsd", $auction_id,$auction_name, $start_date, $end_date, $listing_details, $highest_bid, $state, $buyer_id);
        }

        if ($stmt->execute()) {
            return $this->response('success', 'Auction created successfully', 200);
        } else {
            return $this->response('error', 'Auction creation failed: ' . $stmt->error, 500);
        }
    }
    private function generateAuctionID() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 10;
        $auction_id = '';
        for ($i = 0; $i < $length; $i++) {
            $auction_id .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $auction_id;
    }

    // Response function to return JSON data
    private function response($status, $message, $code) {
        header("Content-Type: application/json");
        http_response_code($code);
        $response = array(
            'status' => $status,
            'message' => $message,
            'timestamp' => time()
        );
        return json_encode($response);
    }
}

class GetAuctionAPI {
    private $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getAuction($data) {
        // Check if auction_id is provided
        $auction_id = $data['auction_id'] ?? null;

        if (!$auction_id) {
            return $this->response('error', 'Auction ID is required', 400);
        }

        // Query the database to get auction details
        $sql = "SELECT * FROM auctions WHERE auction_id = ?";
        $stmt = $this->mysqli->prepare($sql);

        if (!$stmt) {
            return $this->response('error', 'Failed to prepare statement: ' . $this->mysqli->error, 500);
        }

        $stmt->bind_param("i", $auction_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $auction = $result->fetch_assoc();
            
            if ($auction) {
                // Decode listing_details from JSON
                $auction['listing_details'] = json_decode($auction['listing_details'], true);

                return $this->response('success', 'Auction details retrieved successfully', 200, $auction);
            } else {
                return $this->response('error', 'Auction not found', 404);
            }
        } else {
            return $this->response('error', 'Failed to execute statement: ' . $stmt->error, 500);
        }
    }

    private function response($status, $message, $code, $data = null) {
        header("Content-Type: application/json");
        http_response_code($code);
        $response = [
            'status' => $status,
            'message' => $message,
            'timestamp' => time(),
            'data' => $data
        ];
        return json_encode($response);
    }
}


class UpdateAuctionAPI {
    private $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function updateAuction($data) {
        // Check if auction_id is provided
        if (!isset($data['auction_id'])) {
            return $this->response('error', 'Auction ID is required', 400);
        }

        $auction_id = $data['auction_id'];
        $fieldsToUpdate = [];
        $params = [];
        $types = '';

        // Check which fields are provided for update
        if (isset($data['state'])) {
            $fieldsToUpdate[] = 'state = ?';
            $params[] = $data['state'];
            $types .= 's';
        }
        if (isset($data['highest_bid'])) {
            $fieldsToUpdate[] = 'highest_bid = ?';
            $params[] = $data['highest_bid'];
            $types .= 'd';
        }
        if (isset($data['buyer_id'])) {
            $fieldsToUpdate[] = 'buyer_id = ?';
            $params[] = $data['buyer_id'];
            $types .= 'i';
        }

        // If no fields to update
        if (empty($fieldsToUpdate)) {
            return $this->response('error', 'No fields to update', 400);
        }

        // Add auction_id to params for the WHERE clause
        $params[] = $auction_id;
        $types .= 'i';

        // Construct the SQL query
        $sql = "UPDATE auctions SET " . implode(', ', $fieldsToUpdate) . " WHERE auction_id = ?";
        $stmt = $this->mysqli->prepare($sql);

        if (!$stmt) {
            return $this->response('error', 'Failed to prepare statement: ' . $this->mysqli->error, 500);
        }

        // Bind parameters
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return $this->response('success', 'Auction updated successfully', 200);
            } else {
                return $this->response('error', 'No changes made or auction not found', 404);
            }
        } else {
            return $this->response('error', 'Auction update failed: ' . $stmt->error, 500);
        }
    }

    // Response function to return JSON data
    private function response($status, $message, $code, $data = null) {
        header("Content-Type: application/json");
        http_response_code($code);
        $response = array(
            'status' => $status,
            'message' => $message,
            'timestamp' => time()
        );
        if ($data !== null) {
            $response['data'] = $data;
        }
        return json_encode($response);
    }
}


class ListingAPI {
    private $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function AUCTIONS() {
        // Query to get all auctions
        $sql = "SELECT * FROM auctions";
        $result = $this->mysqli->query($sql);

        if ($result) {
            $auctions = [];
            while ($row = $result->fetch_assoc()) {
                // Decode listing_details from JSON
                $row['listing_details'] = json_decode($row['listing_details'], true);
                $auctions[] = $row;
            }
            return $this->response('success', 'Auctions retrieved successfully', 200, $auctions);
        } else {
            return $this->response('error', 'Failed to retrieve auctions: ' . $this->mysqli->error, 500);
        }
    }

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
