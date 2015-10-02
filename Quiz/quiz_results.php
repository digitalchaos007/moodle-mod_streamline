<?php 

$quizId = $_POST['data'];

//Data will contain the StreamLine session ID, Course ID and Quiz ID to obtain the relevant data


$results = new stdClass();
$results->quiz="Quiz 1";
$results->data = array(
		array('q1','3.0'),
		array('q2','3.9'),
		array('q3','3.9'),
		array('q4','3.9'),
	);

echo json_encode($results);

echo $results;


?>