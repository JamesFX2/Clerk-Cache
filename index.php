<?php

use clerk\client\ClerkApi;

header("Access-Control-Allow-Origin: *");

spl_autoload_register(function($className) {
	
	$classEnd = array_pop(explode("\\",$className)); 
	require_once realpath(__DIR__). '/client/' . $classEnd . '.php';
});


$config = file_get_contents("config.json");
$clerk = new ClerkApi($config);
$keyword = strip_tags($_POST["keyword"]);


echo json_encode($clerk->get($keyword));
