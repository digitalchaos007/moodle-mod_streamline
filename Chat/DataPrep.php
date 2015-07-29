<?php

		global $CFG, $DB, $OUTPUT, $HStuList, $StuList, $course;

$a1 = user_has_role_assignment($USER->id,1);
$a2 = user_has_role_assignment($USER->id,2);
$a3 = user_has_role_assignment($USER->id,3);
$a4 = user_has_role_assignment($USER->id,4);
$sadmin = is_siteadmin();

		echo $a1.','.$a2.','.$a3.','.$a4.','.$sadmin.'</br> ---';
		if($a2==1||$a3==1||$a4==1||$sadmin==1){
			$sql =  "SELECT u.username
			FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
			WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			AND cxt.contextlevel =50
			AND cxt.instanceid = c.id
			AND c.shortname = ?
			AND (roleid = 5 OR roleid = 3 )";
			$p = $DB->get_records_sql($sql,array($course->shortname));

			foreach ($p as $k => $v) {
				foreach($v as $r => $o){
					$Sval = bin2hex($o);
					$HStuList .= $Sval.',';
					$StuList .= $o.',';
				}
			}

			$HStuList .= '-';
			$StuList .= '-';
		//	echo '<html><body onload="myFunction()"></body></html>';
			echo 'List of H students :'.$HStuList;
		//	echo '</br>';
			echo 'List of  students :'.$StuList;
		//	echo '</br>';
                      echo 'name: '.$course->shortname;
	}
?>
