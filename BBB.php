<?php
	require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
	require_once(dirname(__FILE__).'/locallib.php');
	require_once(dirname(__FILE__).'/lib.php');

	
		
	global $DB, $CFG, $USER, $COURSE;
	
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
	

		$HStuList = null;
		$StuList  = null;
		$stuval   = bin2hex($USER->username);
	
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

	<script>
	
		/* PHP Variables */
		var meetingRunningUrl = <?php echo json_encode($meetingRunningUrl); ?>;
		var recordings = <?php echo json_encode($recordingsURL); ?>;
		var meetingRunningUrl = <?php echo json_encode($meetingRunningUrl); ?>;
		var moderator = <?php echo json_encode($moderator); ?>;
		var administrator = <?php echo json_encode($administrator); ?>;
		var teacher = <?php echo json_encode($teacher); ?>;
		var end_meeting_url =  <?php echo json_encode($end_meeting_url); ?>; // This is the url request that ends the meeting; MATT
		var meetingEnded = <?php echo json_encode($meetingEnded); ?>;
		
		/* Variables */
		var sessionRunning = false;
		var recordingURL = "";

		$( document ).ready(function() {
					
			/* 	There are 3 screens which may be displayed for the user. There are 3 cases for each screen to be loaded as described below
				CASE 1 - Meeting has not ended, there exists a recording and the user has the relevant permissions (admin, moderator, teacher)
						Load the options screen
				CASE 2 - Meeting has not ended and either:
					The session is running (moderator has joined meeting) OR
					The user has relevant permissions (admin, moderator, teacher)
						Load the live screen
				CASE 3 - All other cases - Meeting has ended or (session not running and user does not have permissions to start a session)
					CASE 3.1 - Recording exists
						Load the playback screen with the recording
					CASE 3.2 - Recording does not exist
						Load the playback screen displaying no recordings exist
			*/		
			
			BBBSessionRunning();
			var hasRecording = isRecording(); 
			/* 	Case 1 - Load options screen */
			if(!meetingEnded && hasRecording && (administrator || moderator || teacher)) {
				console.log("loading options screen");
				$("#liveView").css("height", "0px");
				$("#recordingView").css("display", "none");	
				$("#optionView").css("visibility", "visible");
				$("#top_liveView").css("display", "none");
			/* 	Case 2 - Load live screen */
			} else if(!meetingEnded && (sessionRunning || administrator || moderator || teacher)) {
				console.log("loading live screen");
				$("#liveView").css("height", "100%");
				$("#recordingView").css("display", "none");
				$("#optionView").css("display", "none");
				$("#top_liveView").css("display", "block");
			/* 	Case 3 - Load playback screen */
			} else {
				console.log("loading playback screen");
				$("#liveView").css("display", "none");
				$("#optionView").css("display", "none");
				$("#recordingView").css("visibility", "visible");
				$("#top_liveView").css("display", "none");
				/* Case 3.1 - Load playback displaying the recording */
				if(recordingURL != "") {
					console.log("Recording Response");
					console.log(recordings);
					initRecordings();
				}
				else { /* Case 3.2 - Load playback with no recording message */
					if(meetingEnded) {
						$("#recordingView").html("<p class='session_no_record'> Sorry, this webinar session has been ended! <br> If the session was recorded, please wait while the recording is processed... </p>");
					} else {
						$("#recordingView").html("<p class='session_no_record'> The webinar session is currently not running, please wait for the lecturer and/or moderator to join. <br> No recordings are available for this lecture at this time. </p>");
					}
				}				
			}
			
			console.log(sessionRunning);
			$(".playback_button").click(function() {
				console.log("Clicked Live Button .. now loading live screen");
				$("#recordingView").css("display", "block");
				$("#liveView").css("display", "none");
				$("#optionView").css("display", "none");
				$("#top_liveView").css("display", "none");
				initRecordings();
			});
		
			$(".live_button").click(function() {
				console.log("Clicked Live Button .. now loading live screen");
				$("#liveView").css("height", "100%");
				
				//TODO: find better method to fix the BBB overflow issue
				setTimeout(function() {reSizeFlashClient("99%");}, 3000);
				setTimeout(function() {reSizeFlashClient("100%");}, 6000);
				
				$("#recordingView").css("display", "none");
				$("#optionView").css("display", "none")		
				$("#top_liveView").css("display", "block");
			});
			
			//Scroll to the sessionRecording
			$('html, body').animate({
				scrollTop: $('#region-main').offset().top-50}, 
			1000);
		});
		
		function reSizeFlashClient(value) {
			document.getElementById("flashclient").style.width = value;		
			console.log("Setting Flash Client Width to: " + value);			
		}
		function initRecordings() {
			loadIframe(recordingURL);
			
			var i=0;
			var recordingURLs = []
			
			var createClickHandler = function(url) {
				return function() { 
					document.getElementById('streamline_recording').src=url
				};		
			}
			
			for(i=0;i<recordings.length;i++) {
				recordingURLs[i]=recordings[i].playbacks.presentation.url;				
				
				var div = document.createElement('div');
				div.className = 'section';
				div.id = 's'+(i+1);
				div.innerHTML = 'Section ' + (i+1);
				div.onclick = createClickHandler(recordingURLs[i]);
				document.getElementById('sectionContainer').appendChild(div);
			}
			console.log("RECORDING LIST");
			console.log(recordingURLs);
		}
		
		function loadIframe(url) {
			document.getElementById('streamline_recording').src=url;		
		}
	
		function isRecording() {
				try{
					var url=recordings[0].playbacks.presentation.url;
					recordingURL = url;
					return true;
				}
				catch(e) // This runs when there is error
				{
					return false;
				}			
		}
		
		//added by Matt, ends the meeting completely
		function exitMeeting(){
			
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.open( "GET", end_meeting_url, false );
			xmlHttp.send( null );	
			
			$.get('endmeeting.php?id=<?php echo $id; ?>', function(){
				//successful ajax request
			}).error(function(){
				alert('error... ohh no!');
			});
			
			alert("The webinar session has been ended!");
			window.location.href = "<?php echo($moodle_dir);?>/mod/streamline/view.php?id=<?php echo $id; ?>";  
			
		}
		
		
		function BBBSessionRunning() {
			$.ajax({
				  type: "GET",
				  url: meetingRunningUrl,
				  dataType: "xml",
				  contentType: "text/xml; charset=\"utf-8\"",
				  complete: function(xmlResponse) {
					meetingResponse=xmlResponse.responseXML;
					meetingRunning=meetingResponse.getElementsByTagName("running")[0];
					running=meetingRunning.childNodes[0].nodeValue;
					if(running == "false") {
						sessionRunning = false;
						console.log("Session is not running");
						return false;
					} else if(running == "true") {
						sessionRunning = true;
						console.log("Session is running");
						return true;
					}
				  },
				   async:   false
			});
		}
		
		var sessionRecording = false;
		function BBBRecordRequest() {
			if(sessionRecording) {
				$("#recordStatus").removeClass("recordStatus_On");
				$("#recordStatus").addClass("recordStatus_Off");
				$("#recordStatus").html("This Lecture is not being recorded");
				console.log("Recording has been switched off");
				sessionRecording = false;			
			} else {
				$("#recordStatus").removeClass("recordStatus_Off");
				$("#recordStatus").addClass("recordStatus_On");
				$("#recordStatus").html("Recording currently in progress");
				console.log("Recording has been switched on");
				sessionRecording = true;
			}
		}
				
		</script>
	
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type="text/css" media="screen">
      html, body, #flashclient                { height:50%;}
      body                                    { margin:0; padding:0; }
      #altContent                             { /* style alt content */ }
    </style>
    <script type="text/javascript" src="<?php Print($variable); ?>client/swfobject/swfobject.js"></script>
	
	
    <script type="text/javascript">
      swfobject.registerObject("ChatModule", "11", "expressInstall.swf");
      swfobject.registerObject("BigBlueButton", "11", "expressInstall.swf");
      swfobject.registerObject("WebcamPreviewStandalone", "11", "expressInstall.swf");
      swfobject.registerObject("WebcamViewStandalone", "11", "expressInstall.swf");
    </script>
    <script src="<?php Print($variable);?>/client/lib/jquery-1.5.1.min.js" language="javascript"></script>
    <script src="<?php Print($variable);?>/client/lib/bigbluebutton.js" language="javascript"></script>
    <script src="<?php Print($variable);?>client/lib/bbb_localization.js" language="javascript"></script>
    <script src="<?php Print($variable);?>client/lib/bbb_blinker.js" language="javascript"></script>
    <script src="<?php Print($variable);?>client/lib/bbb_deskshare.js" language="javascript"></script>
    <script type="text/javascript" src="<?php Print($variable);?>client/lib/bbb_api_bridge.js"></script>
    <script type="text/javascript" src="<?php Print($variable);?>client/lib/bbb_api_cam_preview.js"></script>
    <script type="text/javascript" src="<?php Print($variable);?>client/lib/bbb_api_cam_view.js"></script>
    <script type="text/javascript" src="<?php Print($moodle_dir);?>/mod/streamline/3rd-party.js"></script>
  
    <script>
      window.chatLinkClicked = function(url) {
        window.open(url, '_blank');
        window.focus();
      }
      window.displayBBBClient = function() {
        var bbbc = document.getElementById("flashclient");
        var wcpc = document.getElementById("webcampreviewclient");
        wcpc.style.display = "none";
        bbbc.style.display = "block";
      }
      window.displayWCClient = function() {
        console.log("Displaying webcam preview client");
        var wcpc = document.getElementById("webcampreview");
        wcpc.style.display = "block";
      }
      window.onload = function() {
         registerListeners();
      }
	  //window.location.href="<?php Print($variable); ?>bigbluebutton/api/create?meetingID=test-105&checksum=6de5d773b1768d17f30765e606f1869561e2cce0";
	  
	  
    </script>
</head>
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

  <script>
	function toggleFullScreen(elem) {
		if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
			console.log("Setting to fullscreen mode");
			elem.style.width = '100%';
			elem.style.height = '100%';
			if (elem.requestFullScreen) {
				elem.requestFullScreen();
			} else if (elem.mozRequestFullScreen) {
				elem.mozRequestFullScreen();
			} else if (elem.webkitRequestFullScreen) {
				elem.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
			} else if (elem.msRequestFullscreen) {
				elem.msRequestFullscreen();
			}
			switchLayout('StreamLine Lecture');
		} else {		
			console.log("Setting to normal mode");
			elem.style.width = '75%';
			elem.style.height = '650px';
			if (document.cancelFullScreen) {
				document.cancelFullScreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitCancelFullScreen) {
				document.webkitCancelFullScreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			}
		}
	}
	
	$(document).keyup(function(e) {
		 if (e.keyCode == 27) { // escape key maps to keycode `27`
			// <DO YOUR WORK HERE>
			var elem = document.getElementById("middleContainer");
			elem.style.width = '75%';
			elem.style.height = '650px';
			switchLayout('StreamLine')
		}
	});
	
	
	$('.leave_button').click(function() {
		//Add logout/leave javascript here
		// Runs function created by Matt which ends the meeting and redirects to view.php
		//look in endmeeting.php for more info on what this function does
		exitMeeting();
	});
	$('.quiz_button').click(function() {
		//Add quiz javascript here
	});
	$('.fullscreen_button').click(function() {
		toggleFullScreen(document.getElementById('middleContainer'));
	});
  </script>
  
</html>

