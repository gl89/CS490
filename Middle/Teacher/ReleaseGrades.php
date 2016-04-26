<?php
/* 
Gabriel Loterena
4/4/16
CS490 
PHP file that curls a request from the
Front End to the Back End for results of the exams
the students have completed. 
*/
    $frontinfo = file_get_contents('php://input'); 
	
	$log = curl_init();
	curl_setopt($log, CURLOPT_URL, "https://web.njit.edu/~ef33/cs490/back/releaseGrades.php");
    curl_setopt($log, CURLOPT_POST, 1);
    curl_setopt($log, CURLOPT_POSTFIELDS, $frontinfo);
    curl_setopt($log, CURLOPT_FOLLOWLOCATION, 1);
    
	$backend = curl_exec($log);
    curl_close($log);  
    
?>