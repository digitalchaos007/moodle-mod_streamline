<?php
	require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
	require_once(dirname(__FILE__).'/locallib.php');
	require_once(dirname(__FILE__).'/lib.php');

	
		
	global $DB, $CFG, $USER, $COURSE;
	$HStuList = null;
	$StuList  = null;
	$stuval   = bin2hex($USER->username);	
	include 'Chat/DataPrep.php';
	include 'Chat/StartChat.php';
	
	$bbbsession['salt'] = trim($CFG->BigBlueButtonSaltKey);
	$bbbsession['url'] = trim(trim($CFG->ServerURLforBigBlueButton),'/').'/';
	$id = optional_param('id', 0, PARAM_INT);
	$cm = get_coursemodule_from_id('streamline', $id, 0, false, MUST_EXIST);
	$streamline = $DB->get_record('streamline', array('id' => $cm->instance), '*', MUST_EXIST);
	$meeting = $streamline -> meetingid;
	$course = $streamline -> course;
	$ids = $streamline -> id;
	$dash = "-";
	$meetingid=$meeting.$dash.$course.$dash.$ids;
	$meetingRunningUrl = bigbluebuttonbn_getIsMeetingRunningURL( $meetingid, $bbbsession['url'], $bbbsession['salt'] );
	$recordingsURL = bigbluebuttonbn_getRecordingsArray($meetingid, $bbbsession['url'], $bbbsession['salt'] );
	$end_meeting_url = end_meeting();
	
	$userID = $USER->id;
    //$context = context_module::instance($cm->id);
	$context = get_context_instance(CONTEXT_COURSE,$COURSE->id);
	$roles = get_user_roles($context, $USER->id, true);
	
	$participants = $streamline->participants;
	
	if( $streamline->participants == null || $streamline->participants == "[]" ){
    //The room that is being used comes from a previous version
		$moderator = has_capability('mod/streamline:moderate', $context);
	} else {
		$moderator = bigbluebuttonbn_is_moderator($userID, $roles, $participants);
	}
	$administrator = has_capability('moodle/category:manage', $context);
	//104.155.215.138
	$ipaddress = trim($CFG->ServerURLforBigBlueButton);
	$variable2 = substr($ipaddress, 0, strpos($ipaddress, "b"));
	$variable = trim(trim($variable2),'/').'/';
	$moodle_dir = $CFG->wwwroot;
	
	//Determine if user is the teacher
	$teacher_role = user_has_role_assignment($USER->id,3);
	if($teacher_role == 1) {
		$teacher = true;
	} else {
		$teacher = false;
	}

	//Determine if the meeting has ended based on on the 'meetingended' field in the DB
	if($streamline -> meetingended == 1) {
		$meetingEnded = true;
	} else {
		$meetingEnded = false;
	}

	?>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="streamline.css">

 <!--All the Javascript-->
 <?php include 'BBB_SL_S.php';?>

  <body>
	<div id ="top_liveView">
		<div id="recordStatus" class="recordStatus_Off"> This Lecture is not being recorded </div>
		<!--div id="sectionContainer"><div class="section"></div><div class="section"></div></div-->
	</div>
	<div class="units-row units-split">
		<div class="unit-75" id="middleContainer">
			<div id="liveView">
				<div id="flashclient" style="background-color:#EEEEEE;height:100%;width:100%;float:left;">
				   <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="50%" id="BigBlueButton" name="BigBlueButton" align="middle">
					  <param name="movie" value="<?php Print($variable);?>client/BigBlueButton.swf?v=216" />
					  <param name="quality" value="high" />
					  <param name="allowfullscreen" value="true" />
					  <param name="bgcolor" value="#869ca7" />
					  <param name="wmode" value="window" />
					  <param name="allowScriptAccess" value="always" />
					 
						<object type="application/x-shockwave-flash" data="<?php Print($variable); ?>client/BigBlueButton.swf?v=VERSION" width="100%" height="100%" align="middle">
						  <param name="quality" value="high" />
						  <param name="bgcolor" value="#869ca7" />
						  <param name="allowScriptAccess" value="always" />
						  
							<a href="http://www.adobe.com/go/getflashplayer">
							  <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
							</a>
						 
					   </object>
				   
					</object>
				</div>
			</div>
			<div id="recordingView">
				<div id="sectionContainer"></div>
				<iframe id='streamline_recording' width='100%' height='100%' frameborder='0' scrolling='no' marginheight='0' marginwidth='0'></iframe>
			</div>
			<div id="optionView">
				<div class = "option_button playback_button">View Recording</div>
				<div class = "option_button live_button">Start Session</div>
			</div>
		</div>
		<div  class="unit-25" id="rightContainer">
			<div id="webinar_buttons">
				<div id="std_button" class="fullscreen_button">
					<img src="./images/fullscreen_button.png" style='width: 100%; object-fit: contain; visibility:hidden' />
				</div>
				<div id="std_button" class="quiz_button">
					<img src="./images/fullscreen_button.png" style='width: 100%; object-fit: contain; visibility:hidden' />
				</div>
				<div id="std_button" class="leave_button">
					<img src="./images/fullscreen_button.png" style='width: 100%; object-fit: contain; visibility:hidden' />
				</div>
				<!-- input type="button" value="Full Screen" onclick="toggleFullScreen(document.getElementById('middleContainer'))" -->
			</div>
			<div id="chat_module">
					<?php include 'Chat/Chat.php';?>
			</div>
		</div>
	</div>
    <div id="update-display"/>
    <div id="notifications" aria-live="polite" role="region" aria-label="Chat Notifications"></div>
  </body>

  <?php include 'BBB_SL_E.php';?>
  
</html>

