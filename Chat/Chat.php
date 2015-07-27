

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
				$("#chat-area").animate({ scrollTop: $(document).height() }, "fast");
				return false;
			});

	var id = <?=json_encode($cm->id)?>;

	function loading(){
		socket.emit('load',<?=json_encode($stuval)?>,id);
		socket.emit('loadF',<?=json_encode($stuval)?>,id);
	}

	$(function() { $("#sendie").keydown(
		function(event) {  
			if(event.keyCode == 13){
				socket.emit('message',$('#sendie').val(),id,<?=json_encode($stuval)?>,sid);
				$('#sendie').val("");
			}
		}); 
	});

	socket.on('messback', function(message){
		$('#chat-area').append(message);
	});

	socket.on('loaded', function(history){
		$('#chat-area').append(history);
	});


    </script>
</head>

<body onload="loading()">
  <div class="units-row">
		<div id="webinar_buttons">
		<div id="std_button" class="fullscreen_button"></div>
		<div id="std_button" class="quiz_button"></div>
		<div id="std_button" class="leave_button"></div>
		</div>

      <p id="name-area"></p>
      <div id="chat-area">
      </div>
			<span class="label label-outline"></span>
      <p>Your message: </p>
				 <textarea id="sendie" rows="2"></textarea>
 
  </div>
</body>
</html>
