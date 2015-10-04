<?php 

	$quizId = $_POST['data'];

	//Data will contain the StreamLine session ID, Course ID and Quiz ID to obtain the relevant data


	$results = new stdClass();
	$results->quiz="Quiz 1";
	$results->data = array(
			array('q4','0.68'),
			array('q4','0.20'),
			array('q4','0.96')
		);

	echo json_encode($results);

?>