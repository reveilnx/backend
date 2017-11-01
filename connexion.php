<?php

        header('Access-Control-Allow-Origin: *');

       require_once "./database.php";

        // Retrieve specific parameter from supplied URL
        $data    = array();
        
        
        // retrieve url data
        $telephone  = filter_var(htmlspecialchars($_GET['telephone']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
        $mdp  = filter_var(htmlspecialchars($_GET['mdp']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
        
 

	// Attempt to query database table and retrieve data
  	try {
			$sql = "SELECT * FROM clients WHERE telephone='".$telephone."' AND mdp='".$mdp."'";
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
