<?php

$user = "Test User";

$jwt = $this->generateToken($user);

$res = array("token" => $jwt);
