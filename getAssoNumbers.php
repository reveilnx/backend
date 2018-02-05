<?php
header("Access-Control-Allow-Origin: *");

    
     
    // Attempt to query database table and retrieve data
	try {require_once "./database.php";
		$sql = "SELECT id, nom FROM AssoEntreprise;";
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
   	{echo $e->getMessage();
      		throw $e->getMessage();
   	}
   	
   	//$req->close();
	      
       
?>
