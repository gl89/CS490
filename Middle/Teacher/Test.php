<?php

//file for storing a student's exam and grading it
//grades not released until professor releases them
$link = mysqli_connect('sql1.njit.edu','ef33','t9kmA4Yi');
if(!$link){
    die(' Could not connect: '.mysql_error());
}
mysqli_select_db($link,'ef33') or die(mysqli_error());

$request = file_get_contents('php://input');
$Test = json_decode($request);
//print_r($Test);

$TestName = $Test[0];
$sizeArr = Sizeof($Test);
$student = 'student1';
//$TestName = 'EXAMTest1';
$studentTest = $student.$TestName;
$TotalScore = 0;
$TotalPoss = 0;


$sql = "CREATE TABLE `".$studentTest."` (
QuestionNum INT(255) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
Qtype TEXT NOT NULL, 
Question TEXT NOT NULL, 
Ans1 TEXT NOT NULL, 
Ans2 TEXT NOT NULL, 
Ans3 TEXT NOT NULL, 
Ans4 TEXT NOT NULL, 
CrAns TEXT NOT NULL, 
StudAnswer TEXT NOT NULL, 
CorrInc TEXT NOT NULL, 
PointAcq INT(255) NOT NULL,
Points INT(255) NOT NULL,
TestID INT(255) NOT NULL 
)";
mysqli_query($link, $sql);

$sql2 = "INSERT INTO `".$studentTest."` (Qtype, Question, Ans1, Ans2, Ans3, Ans4, CrAns, Points, TestID)
SELECT Qtype, Question, Ans1, Ans2, Ans3, Ans4, CrAns, Points, TestID
FROM `".$TestName."`";
mysqli_query($link, $sql2);

$counter = 0;
for ($i=1; $i<$sizeArr; $i++) {
    $ty = $Test[$i]->qtype;
    //echo ' Edgar ';
    //echo $ty;
    if($ty == 'MC'){
        //echo ' type is MC ';
        $ansCor =  mysqli_query($link,"SELECT CrAns FROM `".$studentTest."` WHERE QuestionNum = '$i'");
        $mc = mysqli_fetch_assoc($ansCor);
        $ansCorr = $mc['CrAns'];
        //echo $ansCorr;
        $ans = $Test[$i]->ans;
        $Point =  mysqli_query($link,"SELECT Points FROM `".$TestName."` WHERE QuestionNum = '$i'");
        $p = mysqli_fetch_assoc($Point);
        $Points = $p['Points'];
        $TotalPoss += $Points;
        mysqli_query($link, "UPDATE `".$studentTest."` SET StudAnswer = '$ans' WHERE QuestionNum = '$i'");
        if($ans == $ansCorr){
            mysqli_query($link, "UPDATE `".$studentTest."` SET CorrInc = 'Correct' WHERE QuestionNum = '$i'");
            mysqli_query($link, "UPDATE `".$studentTest."` SET PointAcq = '$Points' WHERE QuestionNum = '$i'");
            $TotalScore += $Points;
        }else{
            mysqli_query($link, "UPDATE `".$studentTest."` SET CorrInc = 'Incorrect' WHERE QuestionNum = '$i'");
        }
    }//end MC check
    
    if($ty == 'TF'){
        //echo ' type is TF ';
        $ansCor =  mysqli_query($link,"SELECT CrAns FROM `".$studentTest."` WHERE QuestionNum = '$i'");
        $tf = mysqli_fetch_assoc($ansCor);
        $ansCorr = $tf['CrAns'];
        //echo $ansCorr;
        $ans = $Test[$i]->ans;
        $Point2 =  mysqli_query($link,"SELECT Points FROM `".$TestName."` WHERE QuestionNum = '$i'");
        $p2 = mysqli_fetch_assoc($Point2);
        $Points2 = $p2['Points'];
        $TotalPoss += $Points2;
        mysqli_query($link, "UPDATE `".$studentTest."` SET StudAnswer = '$ans' WHERE QuestionNum = '$i'");
        if($ans == $ansCorr){
            mysqli_query($link, "UPDATE `".$studentTest."` SET CorrInc = 'Correct' WHERE QuestionNum = '$i'");
            mysqli_query($link, "UPDATE `".$studentTest."` SET PointAcq = '$Points2' WHERE QuestionNum = '$i'");
            $TotalScore += $Points2;
        }else{
            mysqli_query($link, "UPDATE `".$studentTest."` SET CorrInc = 'Incorrect' WHERE QuestionNum = '$i'");
        }
    }//end TF check
    
    if($ty == 'FI'){
        //echo ' type is FI ';
        $ansCor =  mysqli_query($link,"SELECT CrAns FROM `".$studentTest."` WHERE QuestionNum = '$i'");
        $fi = mysqli_fetch_assoc($ansCor);
        $ansCorr = $fi['CrAns'];
        $Point3 =  mysqli_query($link,"SELECT Points FROM `".$TestName."` WHERE QuestionNum = '$i'");
        $p3 = mysqli_fetch_assoc($Point3);
        $Points3 = $p3['Points'];
        $TotalPoss += $Points3;
        //echo $ansCorr;
        $ans = $Test[$i]->ans;
        //$ans = preg_replace('/\s+/', '', $ans);
        mysqli_query($link, "UPDATE `".$studentTest."` SET StudAnswer = '$ans' WHERE QuestionNum = '$i'");
        if(strcasecmp($ans,$ansCorr) == 0){
            mysqli_query($link, "UPDATE `".$studentTest."` SET CorrInc = 'Correct' WHERE QuestionNum = '$i'");
            mysqli_query($link, "UPDATE `".$studentTest."` SET PointAcq = '$Points3' WHERE QuestionNum = '$i'");
            $TotalScore += $Points3;
        }else{
            mysqli_query($link, "UPDATE `".$studentTest."` SET CorrInc = 'Incorrect' WHERE QuestionNum = '$i'");
        }
    }//end FI check
    
    if($ty == 'OE'){
        //echo ' type is OE ';
        $sizeOE = Sizeof($Test[$i]->ans);
        $qkey = $Test[$i]->qkey;
        $OECorr =  mysqli_query($link,"SELECT CrAns1, CrAns2, CrAns3, CrAns4, CrAns5 FROM OEqst WHERE qKey = '$qkey'");
        $oe = mysqli_fetch_assoc($OECorr);
        (string)$ansCorr1 = $oe['CrAns1'];
        //echo "CorrAns1: ";
        //var_dump($ansCorr1);
        (string)$ansCorr2 = $oe['CrAns2'];
        //echo "CorrAns2: ";
        //var_dump($ansCorr2);
        (string)$ansCorr3 = $oe['CrAns3'];
        //echo "CorrAns3: ";
        //var_dump($ansCorr3);
        (string)$ansCorr4 = $oe['CrAns4'];
        //echo "CorrAns4: ";
        //var_dump($ansCorr4);
        (string)$ansCorr5 = $oe['CrAns5'];
        //echo "CorrAns5: ";
        //var_dump($ansCorr5);
        //echo "<br>";
        $Point4 =  mysqli_query($link,"SELECT Points FROM `".$TestName."` WHERE QuestionNum = '$i'");
        $p4 = mysqli_fetch_assoc($Point4);
        $Points4 = $p4['Points'];
        $TotalPoss += $Points4;

        $corrCounter = 0;
        
        for($j=0; $j<$sizeOE; $j++){
            (string)$ans = $Test[$i]->ans[$j];
            (string)$ans = preg_replace('/\s+/', '', $ans);
            //echo "Ans: ";
            //var_dump($ans);
            //echo "<br>";
            //echo $corrCounter;
            switch ($j) {
                case 0:
                    if($ans == $ansCorr1){
                        $corrCounter++;      
                    }
                    break;
                case 1:
                    if($ans == $ansCorr2){
                        $corrCounter++;
                    }
                    break;
                case 2:
                    if($ans == $ansCorr3){
                        $corrCounter++;
                    }
                    break;
                case 3:
                    if($ans == $ansCorr4){
                        $corrCounter++;
                    }
                    break;
                case 4:
                    if($ans == $ansCorr5){
                        $corrCounter++;
                    }
                    break;
                default:
                    $corrCounter = 0;
                    
            }//end switch
            
        }//end secondary for loop
        
        if($corrCounter == $sizeOE){
            mysqli_query($link, "UPDATE `".$studentTest."` SET CorrInc = 'Correct' WHERE QuestionNum = '$i'");
            mysqli_query($link, "UPDATE `".$studentTest."` SET PointAcq = '$Points4' WHERE QuestionNum = '$i'");
            $TotalScore += $Points4;
        }else{
            mysqli_query($link, "UPDATE `".$studentTest."` SET CorrInc = 'Incorrect' WHERE QuestionNum = '$i'");
        }
        
    }//end OE check
    
    
}//end main for loop

(int)$finalGrade = (int)(($TotalScore / $TotalPoss)*100);
//echo $finalGrade;
$insGrades = "INSERT INTO GradedTests (TestName, Student, PointsAcq, PointsPoss, Grade) VALUES ('$TestName', '$student', '$TotalScore', '$TotalPoss', '$finalGrade')";
$exec = mysqli_query($link, $insGrades);
//header("Location:https://web.njit.edu/~geg7/cs490/Front/swelcome.php");
?>