<?php 

// METHODE POUR SELECTIONNER UN CLIENT DEPUIS OPERATIONS
function getSourceOperation($token)
{
	try
	{	
		require "./database.php";
		$sql = "SELECT source from operations WHERE id=:id;";
		$req = $pdo->prepare($sql);
		$req->bindParam(':id', $token, PDO::PARAM_STR);
		$req->execute();
		while($row  = $req->fetch(PDO::FETCH_OBJ))
		{
			// Assign each row of data to associative array
			$telClient = $row;
		}
		return $telClient;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
		return [];
	}
}

// METHOD TO GET SOLDE
function getSolde($telClient)
{
	try{	
		
		$response = callService("/GetWalletDetails", array(

					"wallet" => $telClient
									
			), "2.0");
			
			// no error
			if($response->WALLET->BAL != null)
			{
				
				return $response->WALLET->BAL;
	
			}
			else return false;
			
	}
	catch(Exception $e)
	{
		 return false;
	}
}





////////////// MAIN

	require_once "./LemonWay.php";
	
	$token = filter_var(htmlspecialchars($_GET['response_wkToken']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$erreur = 1;
	
	if(!empty($token))
	{
		// on vérifie le status de la transaction
		$statut = verifyStatusIN($token);
		
		// succèss
		if($statut->TRANS->HPAY[0]->STATUS == 3)
		{
			// on actualise la base de données
			// 1)  actualise le status de l'opération 
			$state="success";
			update_Operation($token, $state);
			// 2) récupère l'id du client concerné
			$telClient = getSourceOperation($token);

			if(!empty($telClient))
			{
				// 3) actualise le solde du client
				// 3.a récupère l'ancien solde
				$solde = getSolde($telClient->source);

				if($solde != false)
				{
					try
					{
						require "./database.php";
						$sql = "UPDATE clients SET solde=:solde WHERE telephone=:tel";
						$req3 = $pdo->prepare($sql);
						$req3->bindParam(':solde', $solde, PDO::PARAM_STR);
						$req3->bindParam(':tel', $telClient->source, PDO::PARAM_STR);
						
						$req3->execute();
						$erreur = 0;
					}
					catch(PDOException $e)
					{
						echo $e->getMessage();
						$erreur = 1;
					}
				}
				else $erreur = 1;
			}
			else $erreur = 1;
		}
		else $erreur = 1;
	}	
	else $erreur = 1;
			
	if($erreur == 1) include("moneyIn_error.php");
	else include("moneyIn_success.php");

?>
