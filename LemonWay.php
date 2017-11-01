<?php

/* 
* Put the Directkit JSON2 here (your should see "json2" in your URL)
* Make sure your server is whitelisted, otherwise you will receive 403-forbidden
*/

// DELETE AFTER DEVELOPMENT
header('Access-Control-Allow-Origin: *');

define('DIRECTKIT_JSON2', 'https://sandbox-api.lemonway.fr/mb/nxvision2/dev/directkitjson2/service.asmx');
define('WEBKIT', 'https://sandbox-webkit.lemonway.fr/nxvision2/dev/');
define('LOGIN', 'society');
define('PASSWORD', '123456');
define('LANGUAGE', 'fr');
define('UA', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'ua');

/**
 * Only activate it if your PHP server knows how to verify the certifcates.
 * (You will have to configure the CURLOPT_CAINFO option or the CURLOPT_CAPATH option)
 * https://curl.haxx.se/libcurl/c/CURLOPT_SSL_VERIFYPEER.html
 * https://stackoverflow.com/a/18972719/347051
 */ 
define('SSL_VERIFICATION', false);


/*
IP of end-user
*/
function getUserIP() 
{
	$ip = '';
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	elseif (!empty($_SERVER['REMOTE_ADDR'])) 
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	else 
	{
		$ip = "127.0.0.1";
	}
	return $ip;
}



function callService($serviceName, $parameters, $version) 
{
	// add missing required parameters
	$parameters['wlLogin'] = LOGIN;
	$parameters['wlPass'] = PASSWORD;
	$parameters['version'] = $version;
	$parameters['walletIp'] = getUserIP();
	$parameters['walletUa'] = UA;
		
	// wrap to 'p'
	$request = json_encode(array( 'p' => $parameters));
    $serviceUrl = DIRECTKIT_JSON2.'/'.$serviceName;
	$headers = array("Content-type: application/json;charset=utf-8",
					            "Accept: application/json",
					            "Cache-Control: no-cache",
					            "Pragma: no-cache"
                                //"Content-Length:".strlen($request)
					        );
					        		        
	// init curl
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $serviceUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, SSL_VERIFICATION);
    $response = curl_exec($ch);
    $network_err = curl_errno($ch);

    // erreur POST
	if ($network_err) 
	{
		error_log('curl_err: ' . $network_err);
		throw new Exception($network_err);
	}
	// success
	else 
	{
        $httpStatus = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
		if ($httpStatus == 200)  
		{
            $unwrapResponse = json_decode($response)->d;
            $businessErr = $unwrapResponse->E;
            if ($businessErr) 
            {
                error_log($businessErr->Code." - ".$businessErr->Msg." - Technical info: ".$businessErr->Error);
                throw new Exception($businessErr->Code." - ".$businessErr->Msg);
            }
            return $unwrapResponse;
        }
        else 
        { 
           throw new Exception("\nService return HttpStatus ". $httpStatus);
        }
	}

}



function call_addOperation($montant, $commentaire, $type, $src, $trgt)
{
	require "./database.php";
	
	try
	{
		$sql  = "INSERT INTO operations(montant, commentaire, type, source, target) 
				VALUES(:montant, :commentaire, :type, :src, :trgt);";
				
		$req = $pdo->prepare($sql);
		$req->bindParam(':montant', $montant, PDO::PARAM_STR);
		$req->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
		$req->bindParam(':type', $type, PDO::PARAM_STR);
		$req->bindParam(':src', $src, PDO::PARAM_STR);
		$req->bindParam(':trgt', $trgt, PDO::PARAM_STR);
		$req->execute();
		//$req->close();
		
		$wkToken =  $pdo->lastInsertId();
		return $wkToken;

	}		 
	catch(PDOException $e)
	{
		throw new Exception("Error". $e->getMessage());
	}
	
}

function update_Operation($id, $state)
{
	require "./database.php";
	
	try
	{
		$sql  = "UPDATE operations SET etat=:state WHERE id=:id ;";

		$req = $pdo->prepare($sql);
		$req->bindParam(':state', $state, PDO::PARAM_STR);
		$req->bindParam(':id', $id, PDO::PARAM_STR);
		
		$req->execute();
		//$sdf->close();
	}		 
	catch(PDOException $e)
	{
		 throw new Exception("Error". $e->getMessage());
	}
	
}

// METHODE POUR VERIFIER LE STATUT D'UNE OPERATION
function verifyStatusIN($token)
{
	$statut = 0;

		try
		{	
			$response= callService("/GetMoneyInTransDetails", array(
			
						"transactionMerchantToken" => $token
						
				), "2.1");
				
				// statut - succès
				return $response;
		}
		catch(Exception $e)
		{
			 return false;
		}
}

?>
