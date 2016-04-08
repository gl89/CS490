<!DOCTYPE html>
<html>
<head>
	<title>Professor Homepage</title>
</head>
<body>

<?php
	
	$request=file_get_contents('php://input');
	$receive=json_decode($request);	
	
	//print_r($receive);
	//echo "<br>";

	$sizeMC=sizeof($receive->MC);
	$sizeTF=sizeof($receive->TF);
	$sizeOE=sizeof($receive->OE);
	$sizeFI=sizeof($receive->FI);	
	//echo $sizeMC;
	//echo $sizeTF;
	//echo $sizeOE;
	//echo $sizeFI;
	
?>
	<form method="post" action="addTest.php">
		
		<?php
		for($i=0; $i<$sizeMC; $i++){
		?>	
	
	<input type="checkbox" name="question">
			<?php $q=$receive->MC[$i]->Qtext;
			echo "Multiple Choice: ".$i." ".$q."<br>"; ?>	
		 	<?php }; ?>
	
	
	<?php		
		for($i=0; $i<$sizeTF; $i++){
	?>	
	
	<input type="checkbox" name="question">
			<?php $q=$receive->TF[$i]->Qtext;
			echo "True Or False: ".$i." ".$q."<br>"; ?>
		 	<?php }; ?>
	
			
	<?php	
		for($i=0; $i<$sizeOE; $i++){
	?>	
	
	<input type="checkbox" name="question">
			<?php $q=$receive->OE[$i]->Qtext;
			echo "Open Ended: ".$i." ".$q."<br>"; ?>
		 	<?php }; ?>
	
	<?php	
		for($i=0; $i<$sizeFI; $i++){
	?>	
	
	<input type="checkbox" name="question">
			<?php $q=$receive->FI[$i]->Qtext;
			echo "Fill in the Blank: ".$i." ".$q."<br>"; ?>
		 	<?php }; ?>
			
			
	
	</form>
		<input type="submit" class="button"/>
	
	
	</body>	
</html>	