<?php
// Replace with your actual credentials
$consumerKey = 'BXQ3t1QdzBhwuEYCdA8kAb1CF6T5Lg6YHVSIsWeebzj72vGr';
$consumerSecret = 'xcGZDcLdBhAkqtQSek7QXftDUwwOg0qFUtNjTUX3i7UP8Ue7Iec0CqERWADZKIlI';
$shortcode = '400200';  // This is your M-Pesa Paybill or Buy Goods number
$lipaNaMpesaOnlineShortcode = '550626';  // This is your Lipa Na M-Pesa Online shortcode
$lipaNaMpesaOnlinePasskey = 'your_lipa_na_mpesa_online_passkey';  // This is the password for encrypting the request
$initiatorName = 'eliamfurniture';  // This is the username of the M-Pesa account initiating the transaction
$initiatorPassword = 'your_initiator_password';  // This is the password of the M-Pesa account initiating the transaction
$securityCredential = 'R4Hsw7i0GEqrNI5ANnqdf0nvHQbiuCXGpcqx9Gj2p98tuK7KjNoc5oW5vRTvURwlrUtZDzoJ0IBDspOJI96cXqOeUFW/9kITvhRH8o1WKxYWp5v0Yd4T/+xK66r3vpwaN+/pM4QGBq5LmulaxEjHtRMIpFal1MqK0maZUfm9NwOmD4y6iIe+LpF9nniqVhlAmH/r0dvVLGMAs5ZROHjygxVpwu3QHwzXWNo+zFrlJAkCEI2IS9MOmh9pkKTFicbVYLl61awPywrxAjNd+D2RLNKka0edGW27/10p7nmiLy44dcr6nRmuVqCs6FY1AV8VLUvsdxxTyRsC2zuLYDYVkw==';  // This is your security credential for the M-Pesa account initiating the transaction

// Retrieve form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = $_POST['phone_number'];
    $amount = $_POST['amount'];

    // Request authentication token
    $token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init($token_url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "$consumerKey:$consumerSecret");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $result = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $result = json_decode($result);

    $access_token = $result->access_token;

    // Initiate payment request
    $payment_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $payment_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $access_token));

    $timestamp = date('YmdHis');
    $password = base64_encode($lipaNaMpesaOnlineShortcode.$lipaNaMpesaOnlinePasskey.$timestamp);

    $curl_post_data = array(
        'BusinessShortCode' => $lipaNaMpesaOnlineShortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone_number,
        'PartyB' => $lipaNaMpesaOnlineShortcode,
        'PhoneNumber' => $phone_number,
        'CallBackURL' => 'https://your_callback_url.com',  // Replace with your callback URL
        'AccountReference' => 'Test',  // Replace with the transaction description
        'TransactionDesc' => 'Test Payment'  // Replace with the transaction description
    );

    $data_string = json_encode($curl_post_data);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    $curl_response = curl_exec($curl);

    // Handle response
    if ($curl_response === false) {
        $response = curl_error($curl);
    } else {
        $response = json_decode($curl_response, true);
    }

    curl_close($curl);

    // Display response to the user (for demo purposes)
    echo '<pre>';
    print_r($response);
    echo '</pre>';
}
?>
