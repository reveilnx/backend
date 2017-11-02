<?php 
	require_once "./LemonWay.php";
	echo "ERROR";
	
	$token = filter_var(htmlspecialchars($_GET['response_wkToken']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	echo var_dump($_GET);
	echo var_dump($token);
	$state="cancel";
	if(!empty($token))
	{
		try
		{
			update_Operation($token, $state);
		}
		catch(Exception $e)
		{
			Throw new Exception("Error". $e->getMessage());
		}
	}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>NXVision</title>
	<style>
		body
		{
			/*background-color: #ecdfb4;*/
		}
		div
		{
			max-width: 700px;
			margin-top: 10%;
			box-shadow: 2px 2px 5px #aaa;
			background-color: rgba(255,255,255,0.8);
			border-radius: 5px;
			margin-left: auto;
			margin-right: auto;
			padding: 2px;
		}
		h1
		{
			width: 100;
			text-align: center;
			color: red;
			text-shadow: 1px 1px 2px #555;
			font-size: 2.4em;
		}
		p
		{
			text-align: center;
			font-size: 1.5em;
		}
	</style>
</head>

<body>
	<div>
		<h1>Recharge Wallet - Erreur</h1>
		<p>La recharge du wallet n'a pas abouti. Vous pouvez retourner sur l'application pour tenter à nouveau d'alimenter le compte.</p>
	</div>
</body>
</html>

