
<?php 

	header('Access-Control-Allow-Origin: *');
	require_once "./LemonWay.php";
	require_once "./checkTrans.php";

	// retrieve url data
    $telephone  = filter_var(htmlspecialchars($_GET['telephone']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	
	$solde = getSolde($telephone);
	
	try
	{
		$solde = getSolde($telephone);
		
		if($solde == false)
		{
			echo json_encode(array(	'error' => "Erreur accès solde"));
		}
		else echo json_encode(array('success' => $solde));
	}
	catch(Exception $e)
	{
		echo json_encode(array(	'error' => $e->getMessage()));
	}
	

?>
