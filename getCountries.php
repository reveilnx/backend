<?php

	header('Access-Control-Allow-Origin: *');
	
	$json = file_get_contents("countries.json");
	echo $json;
	
?>
