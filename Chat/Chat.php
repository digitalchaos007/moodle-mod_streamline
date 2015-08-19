<?php	$HStuList = null;
	$StuList  = null;
	$stuval   = bin2hex($USER->username);	
	include 'Chat/DataPrep.php';
	include 'Chat/StartChat.php';
?>

<html>

<head>

    <link rel="stylesheet" href="css/kube.min.css" />
 		<link rel="stylesheet" type="text/css" href="kube.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.js"></script>
    <script src="js/kube.min.js"></script>
    <link rel="stylesheet" type="text/css" href="streamline.css">

	<script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
	<script src="http://code.jquery.com/jquery-1.11.1.js"></script>

  <script type="text/javascript">	

			$(document).ready(function() {
				$("#chat").animate({ scrollTop: $('#scroll_down').height() }, "fast");
				return false;
			});

	var id = <?=json_encode($cm->id)?>;

	function loading(){
		socket.emit('load',<?=json_encode($stuval)?>,id);
		socket.emit('loadF',<?=json_encode($stuval)?>,id);
	}

	$(function() { $("#sendie").keydown(
		function(event) {  
			if(event.keyCode == 13 ){
				socket.emit('message',$('#sendie').val(),id,<?=json_encode($stuval)?>,sid);
				$('#sendie').val("");
			}
		}); 
	});

	socket.on('messback', function(message){
		$('#chat').append(message);
$("#chat").animate({ scrollTop: 10000000 }, "slow");
	});

	socket.on('loaded', function(history){
		$('#chat').append(history);
$("#chat").animate({ scrollTop: 10000000 }, "fast");
	});

	window.onbeforeunload = function(e) {
		socket.emit('disconnect',id,<?=json_encode($stuval)?>,sid);
	};


    </script>
</head>

<body onload="loading()">
<div id='scroll_down'>
				<ul id="chat">
				</ul>  
</div>
    <p class="chat_send_msg">Your message: </p>
		<textarea id="sendie" rows="2"></textarea>
</body>
</html>
