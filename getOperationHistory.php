<?php

header("Access-Control-Allow-Origin: *");
require_once "./database.php";

$client  = filter_var(htmlspecialchars($_GET['client']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);

	try
	{
		$sql = "SELECT * from operations WHERE source='".$client."' OR target='".$client."';";
		$req = $pdo->prepare($sql);
		$req->execute();
		
		while($row  = $req->fetch(PDO::FETCH_OBJ))
		{
			// Assign each row of data to associative array
			$data[] = $row;
		}
		
		// Return data as JSON
      	echo json_encode($data);
   	}
   	catch(PDOException $e)
   	{
      	echo json_encode(array(	'error' => $e->getMessage()));
   	}	
?>
