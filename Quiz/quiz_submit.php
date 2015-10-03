<?php

	$qid = $_POST['qid'];
	$sid = $_POST['sid'];
	$cid = $_POST['cid'];
	$stuid = $_POST['stuid'];
	$answers = $_POST['answers'];

	$testFile = fopen("quiz.txt", "w") or die("Unable to open file!");
	fwrite($testFile, $qid.'\n');
	fwrite($testFile, $sid.'\n');
	fwrite($testFile, $cid.'\n');
	fwrite($testFile, $stuid.'\n');
	for($i = 0; $i < sizeof($answers);$i++) {
		fwrite($testFile, $answers[0].'\n');	
	}
	
	fclose($testFile);
 


?>
