<?php

if( !$this->isValid($this->post['id']) ) {
    RestUtility::sendResponse(400, "Bad Request - id is required");
}

if( !$this->isValid($this->post['id']) ) {
    RestUtility::sendResponse(400, "Bad Request - id is required");
}

$id = $this->post['id'];
$pass = $this->post['pass'];

//  Fetch record_id with $id and $pass


//$jwt = $this->generateToken($record_id);
$jwt = '1234';

$res = array(
    "message" => "Success",
    "id" => $id,
    "pass" => $pass,
    "token" => $jwt
);

//$res = array("token" => $jwt);
