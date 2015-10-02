<?php

if(isset($_POST['action'])) {

	$email_address = $_POST['address'];
	$name = $_POST['name'];
	
	echo $email_address;
	echo $name;
	
	$testFile = fopen("quiz.txt", "w") or die("Unable to open file!");
	fwrite($testFile, $name . '\n');
	fwrite($testFile, $email_address . '\n');
	fclose($testFile);
	//do some db stuff...
	//if you echo out something, it will be available in the data-argument of the
	//ajax-post-callback-function and can be displayed on the html-site
	

}

?>
