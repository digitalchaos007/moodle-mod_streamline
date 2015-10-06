<?php 

	
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); 
	global $DB, $CFG, $COURSE;
	
	$qid = $_POST['qid'];
	$sid = $_POST['sid'];
	$cid = $_POST['cid'];
	$correct_answers = $_POST["answer"];
	$string_qid = "'". $qid . "'";
	$string_sid = "'". $sid . "'";
	$string_cid = "'". $cid . "'";
	print_r($_POST);
	echo $correct_answers;
	$sql =  "SELECT id, quizid, streamlineid, courseid, userid, answers FROM mdl_streamline_quiz WHERE streamlineid= '".$sid."' AND courseid= '".$cid."' AND quizid= '".$qid."'";
	$records = $DB->get_records_sql($sql); //array($string_sid ,$string_cid ,$string_qid)

	
	
	$answers_count = 0;
	foreach ($records as $id => $record) {
		$answers[$answers_count] = $record->answers;
		$answers_count++;
	}

	$questions_count = 0;
	$questions = null;
	$group_answers = null;
	$grouped_answer_count = 0;
	echo "string length " . (int) (strlen($correct_answers)-3) . " " . $correct_answers . " ";
	
	
	for($i = 0; $i < strlen($correct_answers)-3; $i++)
	{
		if(substr($correct_answers,$i+1,1) == ".")
		{
			$is_unique = 1;
			for($k = 0; $k < sizeof($questions); $k++)
			{
				if($questions[k] == substr($correct_answers,$i,1))
				{
					$is_unique = 0;
				}
				else
				{
					$questions[$questions_count] = substr($correct_answers,$i,1);
					$questions_count++;
				}

			}
			if($is_unique == 1)
			{
				$group_answers[$grouped_answer_count] = substr($correct_answers,$i,4);
				for($j = $i; $j < strlen($correct_answers)-3; $j++)
				{
					if(substr($correct_answers,$i,1) == substr($correct_answers,$j,1) && substr($correct_answers,$i+1,1) == '.' && substr($correct_answers,$j+1,1) == '.')
					{
						$group_answers[$grouped_answer_count] += substr($correct_answers,$j,4);
					
					}

				}
				$grouped_answer_count++;
			}
		}

	}
	
	
	//sort the answers into groups
	
	
	
	
	//Compare students to answers
	
	
	
	//echo "HELLLOOOOOOOOOOOOOOOOO" . $qid . " " . $sid . " " . $cid . " hELLOOOOOOOOOOOOOOOOOOOO";
	
	
	//$query = mysqli_query("SELECT * FROM mdl_streamline_quiz WHERE streamlineid='". $sid ."' AND courseid='". $cid ."' AND quizid='".$qid ."'");	
	//$answers = $row['answers'];
	//$answers = array("1.1");
	/*$answers_final; // my array which contains all answers with respective answers
	$count = 0;
	for($i = 0; $i < sizeof($answers); $i++)
	{
		$tok = explode(";",$answers[$i]);
		
		for($j = 0; $j < sizeof($tok); $j++)
		{
			$answers_final[$count] = $tok[$j];
			$count = $count + 1;
		}
	
	}


	$unique = array_unique($answers_final); // keeps only unique elements
	$result = array_filter($unique); // removes empty answers
	$result = array_values($result); // fixes array index
	echo print_r($result);
	$answer_counts;
	//$count2 = 0;
	for($i = 0; $i < sizeof($result); $i++)
	{
		$freqs = array_count_values($answers_final);
		//echo "array finals" . print_r($freqs);
		$cnt = $freqs[$result[$i]];
		//$cnt = count(array_filter($answers_final,create_function('$a','return $a=='. $result[$i] .';')));
		echo "Count " . $cnt;
		$answer_counts[$i] = $cnt;
	
	
	}
	//search for other answers with same question number and their counts to determine ratio
	$ratio;
	for($i = 0; $i < sizeof($result); $i++)
	{
		$sum = $answer_counts[$i];
		for($j = 0; $j < sizeof($result); $j++)
		{
			$tok1 = strtok($result[$i] ,".");
			$tok2 = strtok($result[$j] ,".");
			if($tok1 == $tok2 && $i != $j)
			{
				$sum += $answer_counts[$j];
			
			}
			
		}
		$ratio[$i] = $answer_counts[$i] / $sum;
	}
	
	$final;
	
	for($i = 0; $i < sizeof($result); $i++){
	
		$final[$i] = array(strtok($result[$i] ,"."), substr($result[$i], strpos($result[$i], ".") + 1),$ratio[$i]);
	
	}*/
	
	//echo "array finals" . print_r($final);
	
	//print_r($final);
	
	//make unique array http://php.net/manual/en/function.array-unique.php
	//count how many times each element appears in that
	// make new array to fill in these counts
	// do a forloop through using the indexes

	
	//$final;
	//echo print_r("Array " . $group_answers . " ");
	$results = new stdClass();
	$results->quiz="Quiz " . $qid;
	$results->data = array(
			array('q4','0.68'), // (question id, answer id, answer ratio)
			array('q4','0.20'),
			array('q4','0.96')
		);

	echo json_encode($results);

?>