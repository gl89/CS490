<?php
//Permissions no longer an issue;
//$fontinfo = file_get_contents('php://input');
//$Questions = json_decode($frontinfo);

$file = "Class.java"; //prewritten Class name. 

$OE = "public class Class{ public static void say(int n){System.out.println(n);}";
$EC= $OE."public static void main (String[] args){say(2);}}";

file_put_contents($file,$EC, FILE_APPEND);

exec("javac Class.java");//creates the Class.class
$filec = "Class.class";
if(file_exists($filec) == true){ //if it compiled properly it would be there
echo "File was created!";
$java_output = shell_exec("java Class"); #save the output of the java code. Only the last line of the output.
}

echo $java_output;
?>
