<?php

        header('Access-Control-Allow-Origin: *');

        require_once "./database.php";

        // Retrieve specific parameter from supplied URL
        $data    = array();
        
        
        // retrieve url data
        $key  = filter_var(htmlspecialchars($_GET['key']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
        $dataGet  = filter_var(htmlspecialchars($_GET['data']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
        

	// Attempt to query database table and retrieve data
  	try {
			if($key == "telephone")	$sql = "SELECT id FROM clients WHERE telephone='".$dataGet."'";
			if($key == "email")	$sql = "SELECT id FROM clients WHERE email='".$dataGet."'";
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
      		 echo ($e->getMessage());
   	}
   	
 


?>

