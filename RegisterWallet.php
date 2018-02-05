<?php

header("Access-Control-Allow-Origin: *");

	require_once "./LemonWay.php";

	// Sanitise URL supplied values
	$nom	   			= filter_var(htmlspecialchars($_REQUEST['nom']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$prenom	   			= filter_var(htmlspecialchars($_REQUEST['prenom']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$mdp	   			= filter_var(htmlspecialchars($_REQUEST['mdp']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$telephone	    		= filter_var(htmlspecialchars($_REQUEST['telephone']), FILTER_SANITIZE_STRING, FILTER_aFLAG_ENCODE_LOW);
	$civilite	    		= filter_var(htmlspecialchars($_REQUEST['civilite']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$email	   			= filter_var(htmlspecialchars($_REQUEST['email']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$dateNaissance			= filter_var(htmlspecialchars($_REQUEST['dateNaissance']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$lieuNaissance			= filter_var(htmlspecialchars($_REQUEST['lieuNaissance']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$nationalite			= filter_var(htmlspecialchars($_REQUEST['nationalite']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$typeClient	    		= filter_var(htmlspecialchars($_REQUEST['typeClient']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$pays	   			= filter_var(htmlspecialchars($_REQUEST['pays']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$rue	  			= filter_var(htmlspecialchars($_REQUEST['rue']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$CP	   			= filter_var(htmlspecialchars($_REQUEST['CP']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$ville	   			= filter_var(htmlspecialchars($_REQUEST['ville']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$nomAsso	   		= filter_var(htmlspecialchars($_REQUEST['nomAsso']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$SIREN	   			= filter_var(htmlspecialchars($_REQUEST['SIREN']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$rueAsso	   		= filter_var(htmlspecialchars($_REQUEST['rueAsso']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$siteAsso	   		= filter_var(htmlspecialchars($_REQUEST['siteAsso']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$assoDescription	   		= filter_var(htmlspecialchars($_REQUEST['assoDescription']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$CPAsso	   			= filter_var(htmlspecialchars($_REQUEST['CPAsso']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$villeAsso	   		= filter_var(htmlspecialchars($_REQUEST['villeAsso']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$codeAssoClient	   		= filter_var(htmlspecialchars($_REQUEST['codeAssoClient']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$dateAdhesionAsso	   	= filter_var(htmlspecialchars($_REQUEST['dateAdhesionAsso']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	
	if($typeClient == "E" or $typeClient == "A") $isCompany = 0;
	elseif ($typeClient == "C") $isCompany = 1;
	
	$ouvertureCmptBanqueAfrique = "NON";
	$payerOrBeneficiary =  "1";
	
	
	// call service - RegisterWallet	
	try
	{	
		if($isCompany == 1)
		{
			$response = callService("/RegisterWallet", array(
		
					"wallet" => $telephone,
					"clientMail" => $email,
					"clientTitle" => $civilite,
					"clientFirstName" => $prenom,
					"clientLastName" => $nom,
					"street" => $rue,
					"postCode" => $CP,
					"city" => $ville,
					"ctry" => $pays,
					"birthdate" => $dateNaissance,
					"birthcity" => $lieuNaissance,
					"nationality" => $nationalite,
					"mobileNumber" => $telephone,
					"isCompany" => $isCompany
				), "1.1");
		}
		if($isCompany == 0)
		{
				$response = callService("/RegisterWallet", array(
		
					"wallet" => $telephone,
					"clientMail" => $email,
					"clientTitle" => $civilite,
					"clientFirstName" => $prenom,
					"clientLastName" => $nom,
					"street" => $rue,
					"postCode" => $CP,
					"city" => $ville,
					"ctry" => $pays,
					"birthdate" => $dateNaissance,
					"birthcity" => $lieuNaissance,
					"nationality" => $nationalite,
					"mobileNumber" => $telephone,
					"isCompany" => $isCompany,
					"companyName" => $nomAsso,
					"companyWebsite" => $siteAsso, 
					"companyDescription" => $assoDescription,
					"companyIdentificationNumber" => $SIREN
				), "1.1");
		}
		
		
		

		if($response->E == null)
		{
			require_once "./database.php";
			
			try
			{
						
				$sql  = "INSERT INTO clients(nom, prenom, mdp, telephone, dateNaissance, 
				lieuNaissance, typeClient, civilite, nationalite, pays, adresse, CP, 
				ville, email, ouvertureCmptBanqueAfrique, idLemonWay, codeAssoClient, dateAdhesionAssoClient) 
				
				VALUES(:nom, :prenom, :mdp, :telephone, :dateNaissance,
				:lieuNaissance, :typeClient, :civilite, :nationalite, :pays, :adresse, :CP, 
				:ville, :email, :ouvertureCmptBanqueAfrique, :idLemonWay, :codeAssoClient, :dateAdhesionAssoClient);	";
				
				$req = $pdo->prepare($sql);
				$req->bindParam(':nom', $nom, PDO::PARAM_STR);
				$req->bindParam(':prenom', $prenom, PDO::PARAM_STR);
				$req->bindParam(':mdp', $mdp, PDO::PARAM_STR);
				$req->bindParam(':telephone', $telephone, PDO::PARAM_STR);
				$req->bindParam(':dateNaissance', $dateNaissance, PDO::PARAM_STR);
				$req->bindParam(':lieuNaissance', $lieuNaissance, PDO::PARAM_STR);
				$req->bindParam(':typeClient', $typeClient, PDO::PARAM_STR);
				$req->bindParam(':civilite', $civilite, PDO::PARAM_STR);
				$req->bindParam(':nationalite', $nationalite, PDO::PARAM_STR);
				$req->bindParam(':pays', $pays, PDO::PARAM_STR);
				$req->bindParam(':adresse', $rue, PDO::PARAM_STR);
				$req->bindParam(':CP', $CP, PDO::PARAM_STR);
				$req->bindParam(':ville', $ville, PDO::PARAM_STR);
				$req->bindParam(':email', $email, PDO::PARAM_STR);
				$req->bindParam(':codeAssoClient', $codeAssoClient, PDO::PARAM_STR);
				$req->bindParam(':dateAdhesionAssoClient', $dateAdhesionAsso, PDO::PARAM_STR);
				$req->bindParam(':ouvertureCmptBanqueAfrique', $ouvertureCmptBanqueAfrique, PDO::PARAM_STR);
				$req->bindParam(':idLemonWay', $response->WALLET->LWID, PDO::PARAM_STR);
			

				$req->execute();
				$id =  $pdo->lastInsertId();
				 
				
				// INSERTION ASSO
				if($typeClient == "A" || $typeClient == "E")
				{
					try
					{
						$sql2  = "INSERT INTO AssoEntreprise(nom, SIREN, adresse, CP, ville, responsable, site, description)  VALUES(:nom, :SIREN, :adresse, :CP, :ville, :respo, :site, :description);";
						$req2 = $pdo->prepare($sql2);
						$req2->bindParam(':nom', $nomAsso, PDO::PARAM_STR);
						$req2->bindParam(':SIREN', $SIREN, PDO::PARAM_STR);
						$req2->bindParam(':adresse', $rueAsso, PDO::PARAM_STR);
						$req2->bindParam(':CP', $CPAsso, PDO::PARAM_STR);
						$req2->bindParam(':ville', $villeAsso, PDO::PARAM_STR);
						$req2->bindParam(':respo', $id, PDO::PARAM_STR);
						$req2->bindParam(':site', $siteAsso, PDO::PARAM_STR);
						$req2->bindParam(':description', $assoDescription, PDO::PARAM_STR);
						
						$req2->execute();
						echo json_encode(array('success'=>1));
					}		 
					catch(PDOException $e)
					{
						echo json_encode(array(	'error BDD' => $e->getMessage()));
					}
				}
				else echo json_encode(array('success'=>1));
			}		 
			catch(PDOException $e)
			{
				echo json_encode(array(	'error BDD' => $e->getMessage()));
			}
		}
		else echo json_encode(array('error Lemonway' => $response->E));
	}
	
	catch(Exception $e)
	{
		echo json_encode(array(	'error Lemonway' => $e->getMessage()));
	}
	
?>
