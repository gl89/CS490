<?php

   	$frontinfo = file_get_contents('php://input');//from the front end post
	//echo file_get_contents('php://input'); //For testing purposes, front end not posting properly.
	
    $ch = curl_init("https://web.njit.edu/~ef33/cs490/back/backEndLogin.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $frontinfo); //"user=$Username&pass=$Password"
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch); //doesn't print it out it returns as string

	curl_close($ch);
    
?>
 
	
	
	  

    


