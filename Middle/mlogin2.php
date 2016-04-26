<?php
	$frontinfo = file_get_contents('php://input');//from the front end post
	//echo file_get_contents('php://input'); //For testing purposes, front end not posting properly.
	$recieve = json_decode($frontinfo);
    
    $Username = $recieve->Username;
    $Password = $recieve->Password;
	
	/*
	//Two seperate curl commands for the time being, not really understanding multi curl commands.
	//Curl command to connect to NJIT
    $ch = curl_init("https://cp4.njit.edu/cp/home/login"); //URL that we want to load in and the initialization of it. 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$Username&pass=$Password&uuid=0xACA021");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = rtrim(curl_exec($ch)); //doesn't print it out it returns as string
    curl_close($ch);
    
	
    $valid = (strpos($result,"loginok.html") !== false);
		if ($valid) {
			 echo "Successful NJIT login. </br>";  //$_SESSION["message1"] = "Successful NJIT login"; //not working how I thought it would...
		} else {
		   echo "Denied NJIT login. </br>";       //$_SESSION["messsage1"] = "Denied NJIT login";
		}
		 
    curl_close($ch);
	*/
	
	//Passing it along to the back-end.
	$fields = array('Username' => $Username, 'Password' => $Password);
	$send = json_encode($fields);
	  
    $ch = curl_init("https://web.njit.edu/~ef33/cs490/back/backEndLogin.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $send); //"user=$Username&pass=$Password"
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch); //doesn't print it out it returns as string

	curl_close($ch);

	  
?>