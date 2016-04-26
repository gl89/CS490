<?php
	$frontinfo = file_get_contents('php://input');//from the front end post
	//echo file_get_contents('php://input'); //For testing purposes, front end not posting properly.
	$recieve = json_decode($frontinfo);
	

	//Shouldn't matter that different Qtypes don't have a Ans3, Ans4 ect. 
	//We can't spell.
	$question = $recieve->Question;
	$ans1 = $recieve->Ans1;
	$ans2 = $recieve->Ans2;
	$ans3 = $recieve->Ans3;
	$ans4 = $recieve->Ans4;
	$correct = $recieve->Correct;
	$correct1 = $recieve->Correct1;
	$correct2 = $recieve->Correct2;
	$correct3 = $recieve->Correct3;
	$correct4 = $recieve->Correct4;
	$correct5 = $recieve->Correct5;
	$qtype = $recieve->qtype;
	$input1 = $recieve->Input1; 
	$input2 = $recieve->Input2; 
	$input3 = $recieve->Input3; 
	$input4 = $recieve->Input4; 
	$input5 = $recieve->Input5; 
	$points = $recieve->Points; 
	
	
	//I'm the one who will end up determining which back end file to send it to. 
	if($qtype=="TF"){
		$URL="https://web.njit.edu/~ef33/cs490/back/createTFQuestion.php";
				$fields= array(
				"Question"=>$question,
				"Correct"=>$correct,
				"qtype"=>$qtype,
				"Points"=>$points
				);
	}  
	if($qtype=="FI"){
		$URL="https://web.njit.edu/~ef33/cs490/back/createFIQuestion.php";
				$fields= array(
				"Question"=>$question,
				"Correct"=>$correct,
				"qtype"=>$qtype,
				"Points"=>$points
				);
	} 
	if($qtype=="OE"){
				$URL="https://web.njit.edu/~ef33/cs490/back/createOEQuestion.php";
				$fields= array(
				"Question"=>$question,
				"Correct1"=>$correct1,
				"Correct2"=>$correct2,
				"Correct3"=>$correct3,
				"Correct4"=>$correct4,
				"Correct5"=>$correct5,
				"qtype"=>$qtype,
				"Input1"=>$input1,
				"Input2"=>$input2,
				"Input3"=>$input3,
				"Input4"=>$input4,
				"Input5"=>$input5,
				"Points"=>$points
				
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
				"Correct"=>$correct,
				"Points"=>$points
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