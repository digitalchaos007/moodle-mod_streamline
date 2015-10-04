<?php


	global $DB;

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
	

        $N_record1 = new stdClass();
        $N_record1->quizid  = (int) $qid;
        $N_record1->streamlineid = (int) $sid;
        $N_record1->courseid = (int) $cid;
        $N_record1->userid  = (int) $stuid;
        $N_record1->answers = (string) $answers;
        $arr = array($N_record1);

        $table = 'streamline_quiz';
    try {
        $DB->insert_records($table, $arr);
				$error = 'Always throw this error';
				 throw new Exception($error);

    } catch (Exception $e) {
				fwrite($testFile, 'error '.$e.'\n');
    }
 
    fwrite($testFile, 'completed 5 '.'\n');
 
    fclose($testFile);


?>
