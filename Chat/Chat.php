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

// hyperlink feature --
	function HyperLinks(msg){
	    var r = msg.split(" ");
	    var whiteList = ["http","https","www"];

	    for(var x in r){
	      if( StrContains(r[x],whiteList) == true){
	        var link = r[x].link(r[x]);
	        r[x] = link;
	      }	
	    }
	r = r.join(" ");
    	return r;
    }


	function StrContains(val, arr){
	   for(var x in arr){
		
		if(val.indexOf(arr[x])!= -1){
		    return true;
		}
	    }
	    return false;
	}

// End of hyperlink feature --

	$(function() { $("#sendie").keydown(
		function(event) {  
			if(event.keyCode == 13 ){
				socket.emit('message',$('#sendie').val(),id,<?=json_encode($stuval)?>,sid);
				$('#sendie').val("");
			}
		}); 
	});

	socket.on('messback', function(message){
	    var mes = HyperLinks(message);
		$('#chat').append(mes);
$("#chat").animate({ scrollTop: 10000000 }, "slow");
	});

	socket.on('loaded', function(history){
		var mes = HyperLinks(message);
		$('#chat').append(mes);
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
