<?php
/* 
Gabriel Loterena
4/4/16
CS490 
PHP file that curls info from the Front End
to the proper Back End PHP file. 
*/

    $frontinfo = file_get_contents('php://input'); //Just passing the questions chosen for the exam.
	//echo file_get_contents('php://input'); //For testing purposes.
    $recieve = json_decode($frontinfo); //not really necessary
	
	$log = curl_init();
	curl_setopt($log, CURLOPT_URL, "https://web.njit.edu/~ef33/CS490/back/backEnd.php");
    curl_setopt($log, CURLOPT_POST, 1);
    curl_setopt($log, CURLOPT_POSTFIELDS, $recieve);
    curl_setopt($log, CURLOPT_FOLLOWLOCATION, 1);
    
	$backend = curl_exec($log);
    curl_close($log);  
    
?>