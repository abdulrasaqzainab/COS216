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
        if (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*\W).{8}$/', $password)) {
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

        case 'GetAllListings':
            $listingAPI = new ListingAPI($mysqli);
            echo $listingAPI->getAllListings($data);
            break;

        default:
            apiResponse('error', 'Invalid request type', 400);
    }
} else {
    apiResponse('error', 'Invalid request', 400);
}

//listings
class ListingAPI {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getAllListings($data) {
        //extract data from JSON POST body
        $apikey = $data['apikey'];
        $limit = isset($data['limit']) ? intval($data['limit']) : 30;
        $sort = isset($data['sort']) ? $data['sort'] : 'id'; //sorting by id
        $order = isset($data['order']) && strtoupper($data['order']) == 'DESC' ? 'DESC' : 'ASC'; //ascending order
        $fuzzy = isset($data['fuzzy']) ? filter_var($data['fuzzy'], FILTER_VALIDATE_BOOLEAN) : true; //fuzzy search
        $search = isset($data['search']) ? $data['search'] : array();//search by location
        $returnFields = isset($data['return']) ? $data['return'] : array();
        
        //validating return fields
        if (empty($returnFields)) {
            return apiResponse('error', 'ERROR:Return fields are missing', 400);
        }
        
        //whitelist of allowed return fields
        $allowedFields = ['id', 'title', 'location', 'price', 'bedrooms', 'bathrooms', 'url', 'parking spaces', 'amenities', 'description', 'type', 'images'];
        $invalidFields = array_diff($returnFields, $allowedFields);
        if (!empty($invalidFields)) {
            return apiResponse('error', 'ERROR:Invalid return fields: ' . implode(', ', $invalidFields), 400);
        }

        //build the WHERE clause for search
        $where = '';
        $params = array();
        foreach ($search as $key => $value) {
            if (!empty($where)) {
                $where .= " AND ";
            }
            if ($fuzzy) {
                $where .= "`$key` LIKE ?";
                $params[] = "%$value%";
            } else {
                $where .= "`$key` = ?";
                $params[] = $value;
            }
        }

        //prepare the SQL query
        $sql = "SELECT " . implode(", ", $returnFields) . " FROM listings";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY $sort $order LIMIT ?";
        $params[] = $limit;

        //execute the query
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return apiResponse('error', 'ERROR:Failed to prepare statement: ' . $this->mysqli->error, 500);
        }

        //binding params
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        //fetch listings and fetch images using cURL
        $listings = array();
        while ($row = $result->fetch_assoc()) {
            //fetch images using cURL (Replace with your actual image fetching logic)
            $images = $this->fetchImagesForListing($row['id']);
            
            //append fetched images to the listing
            $row['images'] = $images;
            
            //add the listing to the result array
            $listings[] = $row;
        }

        //close the statement
        $stmt->close();

        //return the response
        return apiResponse('success', 'Listings fetched successfully', 200, $listings);
    }


/*
private function fetchImagesForListing($listingId) {
    // Array to store fetched image URLs
    $imageUrls = array();

    // URL to the Wheatley image endpoint
    $imageEndpoint = "https://wheatley.cs.up.ac.za/api/images/houses/";

    // Create cURL resource
    $ch = curl_init();

    // Set cURL options
    $url = $imageEndpoint . $listingId . ".png"; // Construct image URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout in seconds
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json"
    ));

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        // Handle cURL error
        $error = curl_error($ch);
        // Log or handle the error as needed
    } else {
        // Assuming the response contains image URLs as JSON
        $responseData = json_decode($response, true);
        
        // Check if the response is valid and contains image URLs
        if (is_array($responseData) && !empty($responseData)) {
            foreach ($responseData as $image) {
                // Assuming each image URL is under 'url' key
                $imageUrls[] = $image['url'];
            }
        }
    }

    // Close cURL resource
    curl_close($ch);

    // Return array of image URLs
    return $imageUrls;
}
*/
private function fetchImagesForListing($listingId) {
    // Array to store fetched image URLs
    $imageUrls = array();

    // URL to the Wheatley image endpoint
    $imageEndpoint = "https://wheatley.cs.up.ac.za/api/images/houses/";

    // Create cURL resource
    $ch = curl_init();

    // Set cURL options
    $url = $imageEndpoint . $listingId . ".png"; // Construct image URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout in seconds
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json"
    ));

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        // Handle cURL error
        $error = curl_error($ch);
        // Log or handle the error as needed
        // For now, let's log it
        error_log("cURL Error: $error");
    } else {
        // Assuming the response contains image URLs as JSON
        $responseData = json_decode($response, true);
        
        // Check if the response is valid and contains image URLs
        if (is_array($responseData) && !empty($responseData)) {
            foreach ($responseData as $image) {
                // Assuming each image URL is under 'url' key
                $imageUrls[] = $image['url'];
            }
        } else {
            // Log if the response is empty or not in the expected format
            error_log("Invalid or empty response received while fetching images.");
        }
    }

    // Close cURL resource
    curl_close($ch);

    // Return array of image URLs
    return $imageUrls;
}


    }


?>
