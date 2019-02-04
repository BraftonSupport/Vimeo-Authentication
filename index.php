<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('VimeoToken.php');

if(isset($_GET['code']) && isset($_GET['state']) && $_GET['state'] == 'arbitrary') :

	$state = $_GET['state'];
	$code = $_GET['code'];

	$tokenRequest = new VimeoToken($code,$state);
	$response = $tokenRequest->tokenRequest();

	//Build client data array
	$creds = array(
				$response->user->name => array(
					'token' => $response->access_token,
					'link' => $response->user->uri
				)
	);

	$file = '../creds/creds.json';
	$output = file_get_contents($file);
	$temp = json_decode($output, TRUE);
	array_push($temp,$creds);
	file_put_contents($file, json_encode($temp));
	
endif;

?>