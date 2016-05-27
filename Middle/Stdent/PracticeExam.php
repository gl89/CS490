<?php
	/*
	Gabriel Loterena
	4/19/2016
	CS490
	PracticeQuestionHandler.php 
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
			$file = "Class.java"; //prewritten Class name. 
			
			//Values for OE should hold a string containing a java method
			$User_Input = $values; 
			
			//This looks for whatever is front of the '()' aka the method name 
			preg_match('/[A-za-z0-9\_]+\(/', $User_Input, $method);  
			$method = trim($method[0], '()');
		
			//Appends OE user input, the method name, and the test case into string holding java code. 
			$JC= "public class Class{".$User_Input."public static void main(String[] args){".$method."(".$param.");}}";
		
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
			file_put_contents($file,"");
			//deletes Class.class
			if(file_exists($filec) == true){ //if it compiled properly it would be true 
				unlink($filec);
			}
			$Test[] = $output;
			} //END OF LOOP TO TEST FOR 5 TEST CASES
		
		//Same with other qtypes we package everything in an array
		$qtype="OE";
		//qkey was assigned above and used for queries
		$ans=$output; //ans will hold an array I thinks
		$fields = array('qtype' => $qtype, 'qkey' => $key,'ans'=>$Test);
		$QS[]=$fields;
	}
	
	$request = json_encode($QS);
	
	//grading aspect
	$link = mysqli_connect('sql1.njit.edu','ef33','t9kmA4Yi');
	if(!$link){
		die(' Could not connect: '.mysql_error());
	}
	mysqli_select_db($link,'ef33') or die(mysqli_error());

	$Test = json_decode($request);
	
	$TestName = $Test[0];
	$sizeArr = Sizeof($Test);
	$student = 'student1';

	$studentTest = $student.$TestName;
	$TotalScore = 0;
	$TotalPoss = 0;

	$counter = 0;
	for ($i=1; $i<$sizeArr; $i++) {
		$ty = $Test[$i]->qtype;
		if($ty == 'MC'){
			$ansCor =  mysqli_query($link,"SELECT CrAns FROM `".$studentTest."` WHERE QuestionNum = '$i'");
			$mc = mysqli_fetch_assoc($ansCor);
			$ansCorr = $mc['CrAns'];
			$ans = $Test[$i]->ans;
			$Point =  mysqli_query($link,"SELECT Points FROM `".$TestName."` WHERE QuestionNum = '$i'");
			$p = mysqli_fetch_assoc($Point);
			$Points = $p['Points'];
			$TotalPoss += $Points;
			mysqli_query($link, "UPDATE `".$studentTest."` SET StudAnswer = '$ans' WHERE QuestionNum = '$i'");
			if($ans == $ansCorr){
				echo "Correct! Your answer was ".$ans.".<br>";
				$TotalScore += $Points;
			}else{
				echo "Incorrect! Your answer was ".$ans." and the correct answer is ".$ansCorr.".<br>"; 
			}
		}
    
		if($ty == 'TF'){
	
			$ansCor =  mysqli_query($link,"SELECT CrAns FROM `".$studentTest."` WHERE QuestionNum = '$i'");
			$tf = mysqli_fetch_assoc($ansCor);
			$ansCorr = $tf['CrAns'];
	
			$ans = $Test[$i]->ans;
			$Point2 =  mysqli_query($link,"SELECT Points FROM `".$TestName."` WHERE QuestionNum = '$i'");
			$p2 = mysqli_fetch_assoc($Point2);
			$Points2 = $p2['Points'];
			$TotalPoss += $Points2;
			if($ans == $ansCorr){
				echo "Correct! Your answer was ".$ans.".<br>";
				$TotalScore += $Points2;
			}else{
				echo "Incorrect! Your answer was ".$ans." and the correct answer is ".$ansCorr.".<br>"; 
			}
		}//end TF check
    
		if($ty == 'FI'){
	
			$ansCor =  mysqli_query($link,"SELECT CrAns FROM `".$studentTest."` WHERE QuestionNum = '$i'");
			$fi = mysqli_fetch_assoc($ansCor);
			$ansCorr = $fi['CrAns'];
			$Point3 =  mysqli_query($link,"SELECT Points FROM `".$TestName."` WHERE QuestionNum = '$i'");
			$p3 = mysqli_fetch_assoc($Point3);
			$Points3 = $p3['Points'];
			$TotalPoss += $Points3;
		
			$ans = $Test[$i]->ans;
			if(strcasecmp($ans,$ansCorr) == 0){
				echo "Correct! Your answer was ".$ans.".<br>";
				$TotalScore += $Points3;
			}else{
				echo "Incorrect! Your answer was ".$ans." and the correct answer is ".$ansCorr.".<br>"; 
			}
		}//end FI check
    
		if($ty == 'OE'){
			$sizeOE = Sizeof($Test[$i]->ans);
			$qkey = $Test[$i]->qkey;
			$OECorr =  mysqli_query($link,"SELECT CrAns1, CrAns2, CrAns3, CrAns4, CrAns5 FROM OEqst WHERE qKey = '$qkey'");
			$oe = mysqli_fetch_assoc($OECorr);
			(string)$ansCorr1 = $oe['CrAns1'];
			(string)$ansCorr2 = $oe['CrAns2'];
			(string)$ansCorr3 = $oe['CrAns3'];
			(string)$ansCorr4 = $oe['CrAns4'];
			(string)$ansCorr5 = $oe['CrAns5'];
   
        $Point4 =  mysqli_query($link,"SELECT Points FROM `".$TestName."` WHERE QuestionNum = '$i'");
        $p4 = mysqli_fetch_assoc($Point4);
        $Points4 = $p4['Points'];
        $TotalPoss += $Points4;

        $corrCounter = 0;
        
        for($j=0; $j<$sizeOE; $j++){
            (string)$ans = $Test[$i]->ans[$j];
            (string)$ans = preg_replace('/\s+/', '', $ans);
            switch ($j) {
                case 0:
                    if($ans == $ansCorr1){
						"Passed test case: ".$j.".<br>";
                        $corrCounter++;      
                    }
					else{
						echo "Test Case: ".$j." Your output was ".$ans." and the correct output is ".$ansCorr1.".<br>";
					}
                    break;
                case 1:
                    if($ans == $ansCorr2){
						"Passed test case: ".$j."<br>";
                        $corrCounter++;
                    }
					else{
						echo "Test Case: ".$j." Your output was ".$ans." and the correct output is ".$ansCorr2.".<br>";
					}
                    break;
                case 2:
                    if($ans == $ansCorr3){
						"Passed test case: ".$j.".<br>";
                        $corrCounter++;
                    }
					else{
						echo "Test Case: ".$j." Your output was ".$ans." and the correct output is ".$ansCorr3.".<br>";
					}
                    break;
                case 3:
                    if($ans == $ansCorr4){
						"Passed test case: ".$j.".<br>";
                        $corrCounter++;
                    }
					else{
						echo "Test Case: ".$j." Your output was ".$ans." and the correct output is ".$ansCorr4.".<br>";
					}
                    break;
                case 4:
                    if($ans == $ansCorr5){
						"Passed test case: ".$j.".<br>";
                        $corrCounter++;
                    }
					else{
						echo "Test Case: ".$j." Your output was ".$ans." and the correct output is ".$ansCorr5.".<br>";
					}
                    break;
                default:
                    $corrCounter = 0;
            }//end switch
        }//end secondary for loop
        
        if($corrCounter == $sizeOE){
         	echo "Your code passed all test cases! <br>";
            $TotalScore += $Points4;
        }else{
			echo "You're code only passed ".$corrCounter." of ".$sizeOE." test cases. <br>";
        }
    }//end OE check   
}//end main for loop

(int)$finalGrade = (int)(($TotalScore / $TotalPoss)*100);

echo "You got ".$TotalScore." out of ".$TotalPoss." for a grade of ".$finalGrade.".<br>";
echo "<br>";
echo '<a target="iframe1" href="about:blank" style="text-decoration: none">clear</a>';
?>




