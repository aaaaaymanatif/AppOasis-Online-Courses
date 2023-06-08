<?php

session_start();

// Define a constant or variable to allow access to the config file
define('AppOasis_APP_CONFIG', true);

// Include the config file
$config = include 'config.php';

// Retrieve the encryption key
$encryptionKey = $config['encryption_key'];

function encryptString($string, $encryptionKey)
{

    $ivLength = openssl_cipher_iv_length('AES-256-CBC');

    $iv = openssl_random_pseudo_bytes($ivLength);

    $encryptedString = openssl_encrypt(
        $string,
        'AES-256-CBC',
        $encryptionKey,
        OPENSSL_RAW_DATA,
        $iv
    );

    $encryptedString = base64_encode($iv . $encryptedString);

    return $encryptedString;

}

function decryptString($encryptedString, $encryptionKey)
{

    $encryptedString = base64_decode($encryptedString);

    $ivLength = openssl_cipher_iv_length('AES-256-CBC');

    $iv = substr($encryptedString, 0, $ivLength);

    $encryptedString = substr($encryptedString, $ivLength);

    $decryptedString = openssl_decrypt(
        $encryptedString,
        'AES-256-CBC',
        $encryptionKey,
        OPENSSL_RAW_DATA,
        $iv
    );

    return $decryptedString;

}

//Database configuration variables
$servername = $config['servername'];

$username = $config['username'];

$password = $config['password'];

$database = $config['database'];

$courseId = $_POST['courseId'];

$userEmail = decryptString($_SESSION['userEmail'], $encryptionKey);

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {

    die("Connection failed: " . $conn->connect_error);

}

// Set up your PayPal API credentials for sandbox mode
$clientID = 'AdCUflWFrNE6AoZPEakI6iihOwO0ayFptoTc2OgGjnBcr7qqDRDJ45TeVzGJEWB48LSU1kFdquIqLB7B';
$clientSecret = 'EJSpRpty-bCIft2uUxrj2AtuUhBWywpsINUtLyS-Ibx6fjKvzqtU8lZOax1jCJUJ4vn73UPWA0lzZrp6';

// Extract the order ID sent from the client-side
$orderID = $_POST['orderID'];

// Extract the price sent from the client-side
$price = $_POST['price'];

// Set up the API request to verify the payment in sandbox mode
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v2/checkout/orders/' . $orderID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($clientID . ':' . $clientSecret)
)
);

// Make the API call to retrieve the order details in sandbox mode
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    // An error occurred while making the API call
    echo 'Payment verification failed.';
} else {
    // Parse the API response
    $responseData = json_decode($response, true);

    // Check if the 'status' key exists in the response
    if (isset($responseData['status'])) {
        $status = $responseData['status'];

        // Verify the payment status
        if ($status === 'COMPLETED') {
            // Payment is completed, proceed with further validation
            $purchaseUnits = $responseData['purchase_units'];
            if (count($purchaseUnits) === 1) {
                $expectedAmount = $price; // Replace with your expected payment amount
                $receivedAmount = $purchaseUnits[0]['amount']['value'];

                // Compare the expected and received amounts
                if ($receivedAmount == $expectedAmount) {
                    // Payment amount matches, proceed with approving the payment
                    echo 'Payment verification successful. Approved the payment.';
                } else {
                    // Payment amount doesn't match
                    echo 'Payment verification failed. Amount mismatch.';
                }

                $sql = "INSERT INTO `purchased_courses`(`user_email`, `course_id`, `course_price`, `amount_paid`) VALUES ('".$userEmail."','".$courseId."','".$price."','".$receivedAmount."')";

                if ($conn->query($sql) === TRUE) {
                    
                  } else {
                    
                  }


            } else {
                // Invalid purchase units
                echo 'Payment verification failed. Invalid purchase units.';
            }
        } else {
            // Payment is not completed
            echo 'Payment verification failed. Payment status is not completed.';
        }
    } else {
        // 'status' key is not present in the response
        echo 'Payment verification failed. Invalid API response.';
    }
}

?>