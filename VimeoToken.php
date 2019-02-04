<?php

class VimeoToken {

	public $vimeo_code;
	public $vimeo_state;
	public $vimeo_obj;
	public $clientId = {APP_CLIENT_ID};
	public $secret ={APP_CLIENT_SECRET};
	private $keys;

	public function __construct($code,$state){
		$this->vimeo_code = $code;
		$this->vimeo_state = $state;
		$this->setHeaders();
		$this->encodeKeys($this->clientId,$this->secret);
	}

	private function setHeaders(){
		$this->vimeo_obj = array(
			'grant_type'=>'authorization_code',
			'code'=>$this->vimeo_code,
			'redirect_uri'=>'https://tech.brafton.com/vimeo/call/'
		);
	}

	private function encodeKeys($c,$s) {
		$temp_string = $c.':'.$s;
		$this->keys = base64_encode($temp_string);
	}

	public function getAuthorization(){
		return $this->keys;
	}

	public function tokenRequest(){
		$json = json_encode($this->vimeo_obj);
		$base64keys = $this->getAuthorization();
		$crl = curl_init();
		curl_setopt($crl, CURLOPT_URL, 'https://api.vimeo.com/oauth/access_token');
		curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($crl, CURLOPT_POSTFIELDS, $json);                                                                  
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($crl, CURLOPT_HTTPHEADER, array(  
				'Authorization: basic ' .$base64keys,                                                                       
			    'Content-Type: application/json',
			    'Accept: application/vnd.vimeo.*+json;version=3.4'
			)                                                                                                                                
		);
		curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);     
		$result = curl_exec($crl);
		$useful = json_decode($result);
		/*var_dump($useful->access_token);
		die();*/
		$errno = curl_errno($crl);
	    $error = curl_error($crl);
	    curl_close($crl);
	    if($errno > 0) {
			echo 'cURL error: ' . $error;
			return $error;
		} else {
	    	return $useful;
	    }
	}
}