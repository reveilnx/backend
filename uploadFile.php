<?php

	require_once "./LemonWay.php";
	
	$id  = filter_var(htmlspecialchars($_POST['id']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$fileName  = filter_var(htmlspecialchars($_POST['fileName']), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	
	$target_path = "uploads/".$id. "/".$fileName.".jpeg";
 
	if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) 
	{
		echo "Upload and move success";
	} 
	else 
	{
		echo .$target_path."   There was an error uploading the file, please try again!";
	}
	
	
	
?>


