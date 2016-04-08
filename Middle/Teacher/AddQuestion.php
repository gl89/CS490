<?php
	$frontinfo = file_get_contents('php://input');//from the front end post
	//echo file_get_contents('php://input'); //For testing purposes, front end not posting properly.
	$recieve = json_decode($frontinfo);
	
	//print_r($recieve);
	
	//Shouldn't matter that different Qtypes don't have a Ans3, Ans4 ect. 
	//We can't spell.
	$question = $recieve->Question;
	$ans1 = $recieve->Ans1;
	$ans2 = $recieve->Ans2;
	$ans3 = $recieve->Ans3;
	$ans4 = $recieve->Ans4;
	$correct = $recieve->Correct;
	$qtype = $recieve->qtype;
	$input = $recieve->Input; 
	
	//I'm the one who will end up determining which back end file to send it to. 
	if($qtype=="TF"){
		$URL="https://web.njit.edu/~ef33/cs490/back/createTFQuestion.php";
				$fields= array(
				"Question"=>$question,
				"Correct"=>$correct,
				"qtype"=>$qtype
				);
	}  
	if($qtype=="FI"){
		$URL="https://web.njit.edu/~ef33/cs490/back/createFIQuestion.php";
				$fields= array(
				"Question"=>$question,
				"Correct"=>$correct,
				"qtype"=>$qtype
				);
	} 
	if($qtype=="OE"){
				$URL="https://web.njit.edu/~ef33/cs490/back/createOEQuestion.php";
				$fields= array(
				"Question"=>$question,
				"Correct"=>$correct,
				"qtype"=>$qtype,
				"Input"=>$input
				);
	}
	if($qtype=="MC"){
		$URL="https://web.njit.edu/~ef33/cs490/back/createMCQuestion.php";
				$fields= array(
				"Question"=> $question,
				"Ans1"=> $ans1,
				"Ans2"=>$ans2,
				"Ans3"=>$ans3,
				"Ans4"=>$ans4,
				"qtype"=>$qtype,
				"Correct"=>$correct
				);
	}
	$send = json_encode($fields);
	
    $ch = curl_init("$URL");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$send); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch); 

	curl_close($ch);

	
?>