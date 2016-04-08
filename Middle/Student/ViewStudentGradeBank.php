<?php

    $frontinfo = file_get_contents('php://input'); ///from the front end post, should be an array of $Username, $Password, & $Role
	//echo file_get_contents('php://input'); //For testing purposes, front end not posting properly.
    $recieve = json_decode($frontinfo);
	
	$log = curl_init();
	curl_setopt($log, CURLOPT_URL, "https://web.njit.edu/~ef33/CS490/back/backEnd.php");
    curl_setopt($log, CURLOPT_POST, 1);
    curl_setopt($log, CURLOPT_POSTFIELDS, $recieve);
    curl_setopt($log, CURLOPT_FOLLOWLOCATION, 1);
    
	$backend = curl_exec($log);
    curl_close($log);  
    
?>