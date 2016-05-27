<?php
	/*
	Gabriel Loterena
	4/19/2016
	CS490
	QuestionHandler.php 
	This PHP file I believe recieves an array of the anwswers 
	chosen by the student from the Front End and in the case its an
	Open Ended Question uses the student input to attempt and create a
	Java File that is compiled server side. The output is saved as a string 
	and sent to the the Exam Results back end. 
	*/
	
	$frontinfo = file_get_contents('php://input');//from the front end post
	$recieve = json_decode($frontinfo);
	//print_r($recieve);
	//echo "<br>";
	
	$Test = $recieve->TestName;
	$MC = $recieve->MC;
    $TF = $recieve->TF;
    $FI = $recieve->FI;
	$OE = $recieve->OE;
	//Cast it as an array, stdClass Object. Front End gave me all types of arrays.
	$MC = (array)$MC;
    $TF = (array)$TF;
    $FI = (array)$FI;
	$OE = (array)$OE;
	
	//should hold every question, its type, and qkey to its respective db at the end.
	$QS = array();
	
	//Front End sent data in a perculiar way
	//Go through each array and append it to the QS array which
	//will be json encode and sent for grading ex. array(qtype, qkey, ans)
	$QS[]= $Test;//ExamName
	foreach($MC as $key => $values){
		$qtype="MC";
		$qkey=$key;
		$ans=$values;
		$fields = array('qtype' => $qtype, 'qkey' => $key,'ans'=>$ans);
		//print_r($fields);
		$QS[]=$fields;
	}
	foreach($TF as $key => $values){
		$qtype="TF";
		$qkey=$key;
		$ans=$values;
		$fields = array('qtype' => $qtype, 'qkey' => $key,'ans'=>$ans);
		//print_r($fields);
		$QS[]=$fields;
	}
	foreach($FI as $key => $values){
		$qtype="FI";
		$qkey=$key;
		$ans=$values;
		$fields = array('qtype' => $qtype, 'qkey' => $key,'ans'=>$ans);
		//print_r($fields);
		$QS[]=$fields;
	}
	
	//print_r($QS);
	
	/*
	//Open ended requires an attempt to compile the java code. 
	foreach($OE as $key => $values){
		$qtype="OE";
		$qkey=$key;
		$ans="5"; //I would actualy have something else; I believe this would be an array
		$fields = array('qtype' => $qtype, 'qkey' => $key,'ans'=>$ans);
		//print_r($fields);
		$QS[]=$fields;
	}
	*/

	//Every $OE that's part of the Exam 
	foreach($OE as $key => $values){
		
		$qkey=$key;
		//This should end up getting me a test case to use for the method
		$link = mysqli_connect('sql1.njit.edu','ef33','t9kmA4Yi');
		if(!$link){
			die(' Could not connect: '.mysql_error());
		}
		
		//a database connection to get the test cases
		mysqli_select_db($link,'ef33') or die(mysqli_error());
		$query = mysqli_query($link,"SELECT Input1, Input2, Input3, Input4, Input5 FROM OEqst WHERE qKey = '$qkey'");
		$row = mysqli_fetch_assoc($query);
		
		$Test = array();
		
		foreach($row as $param){
			//$param=55; //Testing Purposes 
			$file = "Class.java"; //prewritten Class name. 
			
			//Values for OE should hold a string containing a java method
			$User_Input = $values; 
			//$User_Input = "public static void say(int n){System.out.println(n);}";
			//$User_Input = "public static void sant n){System.out.println(n);}";//incorrect Method should cause compilation error so no Class.class
			
			//This looks for whatever is front of the '()' aka the method name 
			preg_match('/[A-za-z0-9\_]+\(/', $User_Input, $method);  

			$method = trim($method[0], '()');
		
			///testing purposes 
			//$JC= "public class Class{".$User_Input."public static void main(String[] args){say(".$param.");}}";
			//Appends OE user input, the method name, and the test case into string holding java code. 
			
			//if $testcase is null, for example a void method, it should be fine /
			//$JC should hold something that resembles Java Code. 
			$JC= "public class Class{".$User_Input."public static void main(String[] args){".$method."(".$param.");}}";
			//$JC= "public class Class{public static void say(int n){System.out.println(n);} public static void main(String[] args){say(".$param.");}}";
			
			//this will add the method that's held in a string to the java file. 
			file_put_contents($file,$JC, FILE_APPEND);
		
			//this will attempt to compile the code
			exec("javac Class.java");//creates the Class.class
		
			$filec = "Class.class";
		
			if(file_exists($filec) == true){ //if it compiled properly it would be true 
				//echo "File was created!"; 
				$output = shell_exec("java Class"); //save the output of the java code. Other methods can save the whole thing.
			} else {
				$output="DNC";
			}
			
			//This part cleans up multiple OE questions
			//this erases everything within Class.java 
			file_put_contents($file,"");
			//deletes Class.class
			if(file_exists($filec) == true){ //if it compiled properly it would be true 
				unlink($filec);
			}
			$Test[] = $output;
			} //END OF LOOP TO TEST FOR 5 TEST CASES
		
		//END OF THAT LOOP
		//print_r($Test);
		
		//Same with other qtypes we package everything in an array
		$qtype="OE";
		//qkey was assigned above and used for queries
		$ans=$output; //ans will hold an array I thinks
		$fields = array('qtype' => $qtype, 'qkey' => $key,'ans'=>$Test);
		//print_r($fields);
		$QS[]=$fields;
	}

	//print_r($QS);
	//echo "<br>";
	
	$URL="https://web.njit.edu/~ef33/cs490/back/takeExam.php";
	$send = json_encode($QS);
	
    $ch = curl_init("$URL");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$send); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch); 

	curl_close($ch);

?>