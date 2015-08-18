
<?php
/* 
<dateF>
<lable lable-balckF>
<PostT>*/
?>
<html>

<head>
   
    <link rel="stylesheet" href="css/kube.min.css" />
    <link rel="stylesheet" href="css/your-styles.css" />
 
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.js"></script>
    <script src="js/kube.min.js"></script>
	<script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
	<script src="http://code.jquery.com/jquery-1.11.1.js"></script>
  <script type="text/javascript">	
	var id = <?=json_encode($cm->id)?>;

	socket.on('forumback', function(message){
		$('#forum-area').prepend(message);
	});

	socket.on('loadedF', function(history){
		$('#forum-area').append(history);
	});

	function PostF(){
		socket.emit('Forum',$('#ForumSend').val(),id,<?=json_encode($stuval)?>,sid);
		$('#ForumSend').val("");
	}

    </script>
</head>

<body>
  <div class="units-row">
    <div >
      <p class="h3"> 
      </p>
      <p id="Fname-area"></p>
	<span class="label label-outline"></span>
	<textarea id="ForumSend" rows="2"></textarea>
	<button class="btn-blue" style="width:100%"onclick="PostF()">Send</button>
      <div id="chat-wrap">
        <div id="forum-area">
        </div>
      </div>
    </div>
  </div>
</body>


</html>
