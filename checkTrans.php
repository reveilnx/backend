<?php 



function verifyStatusOUT($transID)
{
	$statut = 0;

		try
		{	
			$response= callService("/GetMoneyOutTransDetails", array(
			
						"transactionId" => $transID
						
				), "1.4");
				
				// statut - succès
				if($response->TRANS->HPAY[0]->STATUS == "3") return true;
				else return false;
		}
		catch(Exception $e)
		{
			 return false;
		}
}


function verifyStatusPTOP($transID)
{
	$statut = 0;

		try
		{	
			$response= callService("/GetPaymentDetails", array(
			
						"transactionId" => $transID
						
				), "1.0");
				
				// statut - succès
				if($response->TRANS->HPAY[0]->STATUS == "3") return true;
				else return false;
		}
		catch(Exception $e)
		{
			 return false;
		}
}

 

// METHODE POUR SELECTIONNER UN CLIENT DEPUIS OPERATIONS
function getConcernedOperation($token)
{			
	try
	{	
		require "./database.php";
		$sql = "SELECT target,source from operations WHERE id=:id;";
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


// MAIN FUNCTIONS
function check_moneyOut($transID, $token)
{
	if(!empty($transID))
	{			
		if(verifyStatusOUT($transID))
		{				
			//1)  actualise le status de l'opération 
			$state="success";
			update_Operation($token, $state);
			// 2) récupère l'id du client concerné
			$telClient = getConcernedOperation($token);
					
			if(!empty($telClient))
			{
				
			
				// 3) actualise le solde du client
				// 3.a récupère l'ancien solde
				$solde = getSolde($telClient->target);
						
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
						return true;
					}
					catch(PDOException $e)
					{
						return false;
					}
				}
				else return false;
			}
			else return false;
		}
		else return false;
	}
	else return false;
}


function check_PtoP($transID, $token)
{
	if(!empty($transID))
	{			
		if(verifyStatusPTOP($transID))
		{				
			//1)  actualise le status de l'opération 
			$state="success";
			update_Operation($token, $state);
			// 2) récupère l'id du client concerné
			$telClient = getConcernedOperation($token);
				
			if(!empty($telClient))
			{
				
			
				// 3) actualise le solde du client
				// 3.a récupère l'ancien solde
				$soldeTarget = getSolde($telClient->target);
				$soldeSource = getSolde($telClient->source);
						
				if($soldeSource != false)
				{
					try
					{
						require "./database.php";
						$sql = "UPDATE clients SET solde=:solde WHERE telephone=:tel";
						$req3 = $pdo->prepare($sql);
						$req3->bindParam(':solde', $soldeSource, PDO::PARAM_STR);
						$req3->bindParam(':tel', $telClient->source, PDO::PARAM_STR);
						
						$req3->execute();
						
						try
						{
							require "./database.php";
							$sql = "UPDATE clients SET solde=:solde WHERE telephone=:tel";
							$req3 = $pdo->prepare($sql);
							$req3->bindParam(':solde', $soldeTarget, PDO::PARAM_STR);
							$req3->bindParam(':tel', $telClient->target, PDO::PARAM_STR);
							
							$req3->execute();
							return true;
						}
						catch(PDOException $e)
						{
							return false;
						}
					}
					catch(PDOException $e)
					{
						return false;
					}
				}
				else return false;
			}
			else return false;
		}
		else return false;
	}
	else return false;
}
					
?>
