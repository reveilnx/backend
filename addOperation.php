<?php 

	require_once "./LemonWay.php";
	require_once "./checkTrans.php";
	



	
	
// -------------------------------------------------------- call service - MoneyInWebInit
function call_MoneyInWebInit($amountTot, $token, $wallet, $comment)
{
	try
	{	
		$response = callService("/MoneyInWebInit", array(
		
					"wallet" => $wallet,
	//				"amountCom" => $com,
					"amountTot" => $amountTot,
					"comment" => $comment,
					"wkToken" => $token,
					"returnUrl" => "http://92.91.136.106:8080/scripts/return_successIN.php",
					"errorUrl" => "http://92.91.136.106:8080/scripts/moneyIn_error.php",
					"cancelUrl" => "http://92.91.136.106:8080/scripts/moneyIn_error.php",
					"autoCommission" => "0"
			), "1.3");
			
			// no error
			if($response->MONEYINWEB != null)
			{
				// add transaction ID inside BDD
				update_Operation($token, "en cours ".$response->MONEYINWEB->ID);
				// redirection
				return json_encode(array('success'=>$response));
			}
			else return json_encode(array('error' => "erreur LemonWay"));
				
	}
	catch(Exception $e)
	{
		 return json_encode(array('error' => $e->getMessage()));
	}

}


// ------------------------------------------------------ call service - moneyOut
function call_MoneyOut($amountTot, $token, $wallet, $comment)
{
	try
	{	
		
		$response = callService("/MoneyOut", array(

					"wallet" => $wallet,
					"amountTot" => $amountTot,
					"message" => $comment,
									
			), "1.1");
			
			// no error
			if($response->TRANS != null)
			{
				// add transaction ID inside BDD
				update_Operation($token, "en cours ".$response->TRANS->HPAY->ID);
				
				// on vérifie le succès				
				if(check_moneyOut($response->TRANS->HPAY->ID, $token))
				{
					return json_encode(array('success' => $response));
				}
				else return json_encode(array('error' => "erreur BDD"));
	
			}
			else return json_encode(array('error' => "erreur LemonWay"));
			
	}
	catch(Exception $e)
	{
		 return json_encode(array('error' => $e->getMessage()));
	}
}




// -------------------------------------------------------- call service - sendPayment
function call_sendPayment($source, $target, $amount, $token, $comment)
{
	try
	{	
		$response = callService("/SendPayment", array(

					"debitWallet" => $source,
					"creditWallet" => $target,
					"amount" => $amount,
					"message" => $comment
									
			), "1.0");
			
			// no error
			if($response->TRANS_SENDPAYMENT != null)
			{
				// add transaction ID inside BDD
				update_Operation($token, "en cours ".$response->TRANS->HPAY->ID);
				
				// on vérifie le succès				
				if(check_PtoP($response->TRANS_SENDPAYMENT->HPAY->ID, $token))
				{
					return json_encode(array('success' => $response));
				}
				else return json_encode(array('error' => "erreur BDD"));
	
			}
			else return json_encode(array('error' => "erreur LemonWay"));
	}
	catch(Exception $e)
	{
		 return json_encode(array('error' => $e->getMessage()));
	}
}


	
	
	
/////////////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////// MAIN ///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
	
	// Sanitise URL supplied values
	$id	   			= filter_var(htmlspecialchars($_REQUEST['id']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$source	   			= filter_var(htmlspecialchars($_REQUEST['source']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$target	   			= filter_var(htmlspecialchars($_REQUEST['target']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$amountTot	   		= filter_var(htmlspecialchars($_REQUEST['montant']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$comment	   		= filter_var(htmlspecialchars($_REQUEST['commentaire']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$type	   			= filter_var(htmlspecialchars($_REQUEST['type']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	//$commission	   			= filter_var(htmlspecialchars($_REQUEST['commission']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	
	// 1st - add this operation in our block Chain 
	try
	{
		$wkToken = call_addOperation($amountTot,  $comment, $type, $source, $target);
		
		// then call the right service
		if(!empty($wkToken) && !empty($type))
		{
			switch($type)
			{
				// Money In
				case "moneyIn":
					echo call_MoneyInWebInit($amountTot, $wkToken, $source, $comment);
				break;
				
				// Money Out
				case "moneyOut":
					echo call_MoneyOut($amountTot, $wkToken, $target, $comment);
				break;
				
				// P2P
				case "P2P":
					echo call_sendPayment($source, $target, $amountTot, $wkToken, $comment);
				break;
			}
			
		}
		else json_encode(array(	'error' => "l'enregistrement de l'opération a échoué"));
		
	}
	catch(Exception $e)
	{
		echo json_encode(array(	'error' => $e->getMessage()));
	}
	
	

?>
