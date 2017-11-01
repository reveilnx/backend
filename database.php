<?php

	// Define database connection parameters
	$host	=	"127.0.0.1";
	$user	= 	"root";
	$pwd	=	"12AZqs,;:!";
	$db	=	"NXVision";
	$cs	=	"utf8";


	// Set up the PDO parameters
	$dsn	=	'mysql:host='. $host .';port=3306;dbname='. $db .';charset='. $cs;
	$opt = array(
			PDO::ATTR_ERRMODE		=>	PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE	=>	PDO::FETCH_OBJ,
			PDO::ATTR_EMULATE_PREPARES	=>	false,
		);


	// Create a PDO instance (connect to the database)
	$pdo = new PDO($dsn, $user, $pwd, $opt);
?>
