<?php

	require_once "./LemonWay.php";
	

function call_RegisterIBAN($wallet, $holder, $bic, $iban, $id)
{
	try
	{	
		$response = callService("/RegisterIBAN", array(
		
					"wallet" => $wallet,
					"holder" => $holder,
					"bic" => $bic,
					"iban" => $iban
					//dom1
					//dom2
			), "1.1");
			
		if($response->IBAN_REGISTER != null) 
		{
			require "./database.php";
			
			try
			{
				$sql  = "UPDATE clients  SET  IBANfrance=:IBANfrance, BICfrance=:BICfrance	WHERE	id=:id ;";
						
						$req =  $pdo->prepare($sql);
						$req->bindParam(':id', $id , PDO::PARAM_STR);
						$req->bindParam(':IBANfrance', $iban, PDO::PARAM_STR);
						$req->bindParam(':BICfrance', $bic, PDO::PARAM_STR);
						$req->execute();
						
						return json_encode(array('success' => 1));
			}
			catch(PDOException $e)
			{
				return json_encode(array('error' => $e->getMessage()));
			}
		}
		else return json_encode(array('error' => $e->getMessage()));
				
	}
	catch(Exception $e)
	{
		 return json_encode(array('error' => $e->getMessage()));
	}

}



///////////// MAIN

	$id = filter_var(htmlspecialchars($_POST['id']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$wallet = filter_var(htmlspecialchars($_POST['wallet']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$nom = filter_var(htmlspecialchars($_POST['nom']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$prenom = filter_var(htmlspecialchars($_POST['prenom']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$IBANfrance = filter_var(htmlspecialchars($_POST['IBANfrance']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$BICfrance = filter_var(htmlspecialchars($_POST['BICfrance']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$holder = $nom ." ". $prenom;
	//$devise
	
	echo call_RegisterIBAN($wallet, $holder, $BICfrance, $IBANfrance, $id);
	
	
?>
