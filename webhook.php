<?php
$body = file_get_contents('php://input');
$headers = array();
foreach($_SERVER as $i=>$val) {
	$name = str_replace(array('HTTP_', '_'), array('', '-'), $i);
	$headers[$name] = $val;
}

$access_key = '913ED165BB488097C88D';
$secret_key = '3e567147712dd889b8b9cbbb3713ff3a453a7fb8a74a7b0f4335f43f12971e6e881bdfcecd82f415';

$path = 'https://maesh-php-backend.herokuapp.com/webhook.php'; // Replace with your webhook URL
$salt = $headers['SALT'];
$timestamp = $headers['TIMESTAMP'];

$sig_string = "$path$salt$timestamp$access_key$secret_key$body";

$hash_sig_string = hash_hmac("sha256", $sig_string, $secret_key);
$signature = base64_encode($hash_sig_string);

$check_sign = ['rapyd_signature' => $headers['SIGNATURE'], 'generated_signature'=> $signature];

$sign_dump = print_r( $check_sign, true );
$fp_sign = file_put_contents( 'check_sign.log', $sign_dump );
$body_dump = print_r( $body, true );
$fp_headers = file_put_contents( 'body.log', $body_dump );
$headers_dump = print_r( $headers, true );
$fp_headers = file_put_contents( 'headers.log', $headers_dump );

?>