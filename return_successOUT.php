<?php 
function verifyStatusOUT($token)
{
	$statut = 0;

		try
		{	
			$response= callService("/GetMoneyOutTransDetails", array(
			
						"transactionId" => $token
						
				), "1.4");
				
				// statut - succès
				if($response->TRANS->HPAY->STATUS == "3") return true;
				echo return false;
		}
		catch(Exception $e)
		{
			 return false;
		}
}


// //////// MAIN
	$transID = filter_var(htmlspecialchars($_GET['transID']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	echo print_r($_GET);
	
	/*if(!empty($transID))
	{
		
		if(verifyStatusOUT($transID))
		{
			// SUCCES
			try
			{
				$state="success";
				$idClient = "";
				update_Operation($token, $state);
				
				try
				{
					// sélectionne le client
					require "./database.php";
					$sql = "SELECT source from operations WHERE id=:id;";
					$req = $pdo->prepare($sql);
					$req->bindParam(':id', $token, PDO::PARAM_STR);
					$req->execute();
					while($row  = $req->fetch(PDO::FETCH_OBJ))
					{
						// Assign each row of data to associative array
						$idClient = $row;
					}
					
					// récupère l'ancien solde
					try
					{
						$solde = 0;
						
						$sql = "SELECT solde from clients WHERE id=:id;";
						$req2 = $pdo->prepare($sql);
						$req2->bindParam(':id', $idClient, PDO::PARAM_STR);
						$req2->execute();
						while($row2  = $req2->fetch(PDO::FETCH_OBJ))
						{
							// Assign each row of data to associative array
							$solde = $row2;
						}
						
						$solde = $solde - $amount;
						try
						{							
							$sql = "UPDATE clients SET solde=:solde WHERE id=:id;";
							$req3 = $pdo->prepare($sql);
							$req3->bindParam(':id', $idClient, PDO::PARAM_STR);
							$req3->bindParam(':solde', $solde, PDO::PARAM_STR);
							$req3->execute();
							return json_encode(array('success'=>1));
						}
						catch(PDOException $e)
						{
							Throw new Exception("Error". $e->getMessage());
						}
						
					}
					catch(PDOException $e)
					{
						Throw new Exception("Error". $e->getMessage());
					}
				}
				catch(PDOException $e)
				{
					Throw new Exception("Error". $e->getMessage());
				}
			}
			catch(Exception $e)
			{
				Throw new Exception("Error". $e->getMessage());
			}
		}
		else $erreur = 1;
	
	}
	else $erreur = 1;
	
	
	if($erreur == 1) include("moneyIn_error.php");*/

?>
