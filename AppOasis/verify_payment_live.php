<?php

// Set up your PayPal API credentials
$clientID = 'AdCUflWFrNE6AoZPEakI6iihOwO0ayFptoTc2OgGjnBcr7qqDRDJ45TeVzGJEWB48LSU1kFdquIqLB7B';
$clientSecret = 'EJSpRpty-bCIft2uUxrj2AtuUhBWywpsINUtLyS-Ibx6fjKvzqtU8lZOax1jCJUJ4vn73UPWA0lzZrp6';

// Extract the order ID sent from the client-side
$orderID = $_POST['orderID'];

// Set up the API request to verify the payment
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.paypal.com/v2/checkout/orders/' . $orderID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($clientID . ':' . $clientSecret)
));

// Make the API call to retrieve the order details
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    // An error occurred while making the API call
    echo 'Payment verification failed.';
} else {
    // Parse the API response
    $responseData = json_decode($response, true);

    var_dump($responseData);

    // Check if the 'status' key exists in the response
    if (isset($responseData['status'])) {
        $status = $responseData['status'];

        // Verify the payment status
        if ($status === 'COMPLETED') {
            // Payment is completed, proceed with further validation
            $purchaseUnits = $responseData['purchase_units'];
            if (count($purchaseUnits) === 1) {
                $expectedAmount = '0.15'; // Replace with your expected payment amount
                $receivedAmount = $purchaseUnits[0]['amount']['value'];

                // Compare the expected and received amounts
                if ($receivedAmount == $expectedAmount) {
                    // Payment amount matches, proceed with approving the payment
                    echo 'Payment verification successful. Approved the payment.';
                } else {
                    // Payment amount doesn't match
                    echo 'Payment verification failed. Amount mismatch.';
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
