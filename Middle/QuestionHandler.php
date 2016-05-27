<?php
	/*
	Gabriel Loterena
	3/31/2016
	CS490
	QuestionHandler.php 
	This PHP file I believe recieves an array of the anwswers 
	chosen by the student from the Front End and in the case its an
	Open Ended Question uses the student input to attempt and create a
	Java File that is compiled server side. The output is saved as a string 
	and sent to the the Exam Results back end. 
	*/
	
	//This should end up getting me a test case to use for the method
	$link = mysqli_connect('sql1.njit.edu','ef33','t9kmA4Yi');
	if(!$link){
        die(' Could not connect: '.mysql_error());
	}
	
	//a database connection to get the test cases
	$key = 0;
	mysqli_select_db($link,'ef33') or die(mysqli_error());
	$query = mysqli_query($link,"SELECT Input FROM OEqst WHERE qKey = '$key'");
	$row = mysqli_fetch_assoc($query);
	
	//echo $row['Input'];
	//$param=$row['Input']; //For multiple cases add loop; 
	$param=5; //Testing Purposes 
	
	//Still waiting on Front End work.
	//$frontinfo = file_get_contents('php://input');//from the front end post
	//$recieve = json_decode($frontinfo);
	
	//Probably will just be receiving question number,question type and answer chosen;
	//$question = $recieve->Question;
	//$Ans = $recieve->Ans;
	//$qtype = $recieve->qtype;
	
	/*
	//If it's TF, FI, or MC send it to the exam results database.
	if($qtype=="TF" || "FI" || "MC"){
		$fields= array(
			"Question"=>$question,
			"Correct"=>$correct,
			"qtype"=>$qtype
		);
	}  
	*/
	
	//Open ended requires an attempt to compile the java code. 
	//if($qtype=="OE"){
			
		$file = "Class.java"; //prewritten Class name. 
		
		//placeholder for the student's method.
		$OE = "public static void say(int n){System.out.println(n);}";
		//$OE = "public static void sant n){System.out.println(n);}";//incorrect Method should cause compilation error so no Class.class
		
		//This looks for whatever is front of the '()' aka the method name 
		preg_match('/[a-z]+\(/', $OE, $method);
		$method = trim($method[0], '()');
		
		//echo $method;
		
		///testing purposes 
		$EC= "public class Class{".$OE."public static void main(String[] args){say(2);}}";
		//Appends OE user input, the method name, and the test case into string holding java code. 
		//if $testcase is null, for example a void method, it should be fine 
		$EC= "public class Class{".$OE."public static void main(String[] args){".$method."(".$param.");}}";
	
		//this will add the method that's held in a string to the java file. 
		file_put_contents($file,$EC, FILE_APPEND);
		
		//this will attempt to compile the code
		exec("javac Class.java");//creates the Class.class
		
		$filec = "Class.class";
		
		if(file_exists($filec) == true){ //if it compiled properly it would be true 
		echo "File was created!";
		$output = shell_exec("java Class"); #save the output of the java code. Other methods can save the whole thing.
		} else {
		echo "Did not compile!";
		}
   
		//encode everything again;
		//$fields= array(
		//	"Question"=>$question,
		//	"Correct"=>$correct,
		//	"qtype"=>$qtype
		//);
		//}
	
	echo $output;
	
	//This part cleans up multiple OE questions
	//this erases everything within Class.java 
	 file_put_contents($file,"");
    //deletes Class.class
    if(file_exists($filec) == true){ //if it compiled properly it would be true 
		unlink($filec);
	}

	/*
	//$URL="https://web.njit.edu/~ef33/cs490/back/createOEQuestion.php";
	//$send = json_encode($fields);
	
    //$ch = curl_init("$URL");
    //curl_setopt($ch, CURLOPT_POST, 1);
    //curl_setopt($ch, CURLOPT_POSTFIELDS,$send); 
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //$result = curl_exec($ch); 

	  //curl_close($ch);
	 */

?>