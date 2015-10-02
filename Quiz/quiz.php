<!-- Quiz -->

<!-- Modal -->
<div class="modal fade" id="quizModal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-header header-shadow">
			<button type="button" id="modal_close" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Quiz</h4>
		</div>
		<div class="modal-body">
		
			<form id="quizForm">
			</form>
			
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	  
	</div>
</div>

<script>

	function loadXMLDoc(filename)
	{
		if (window.XMLHttpRequest)
		{
			xhttp=new XMLHttpRequest();
		}
		else // code for IE5 and IE6
		{
			xhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xhttp.open("GET",filename,false);
		xhttp.send();
		return xhttp.responseXML;
	}
	
	$(".quiz_button").hover(function() {
		$( ".quiz_button" ).toggleClass( "open" )
		if($("#dropdownMenu1").attr("aria-expanded") == true) {
			$("#dropdownMenu1").attr("aria-expanded", false)		
		} else {
			$("#dropdownMenu1").attr("aria-expanded", true)
		}
	}, function() {
		$( ".quiz_button" ).toggleClass( "open" )
		if($("#dropdownMenu1").attr("aria-expanded") == true) {
			$("#dropdownMenu1").attr("aria-expanded", false)		
		} else {
			$("#dropdownMenu1").attr("aria-expanded", true)
		}
	});

	// XML string to JSON    
	var x2js = new X2JS(); 
	var xml = loadXMLDoc('Quiz/quiz_example.xml');
	var xmlText = new XMLSerializer().serializeToString(xml);
	var quizJSON = x2js.xml_str2json( xmlText );

	for(i=0; i< quizJSON.quizzes.quiz.length; i++) {
		var quiz = "<li class='quizOption' data-toggle='modal' data-target='#quizModal' onclick='populateQuiz("+(i)+")'>Quiz "+(i+1)+"</li>";
		$("#quiz_menu").append(quiz);
	}
	
	function populateQuiz(id) {
	
		$('.modal-title').text("Quiz " + (id+1));
		
		//Check if a quiz with the specified ID exists
		if(i <= quizJSON.quizzes.quiz.length) {
			$("#quizForm").empty();
			for(i=0; i<quizJSON.quizzes.quiz[id].question.length; i++) {
				$("#quizForm").append("<b>Question " + (i+1) + "</b> : " + quizJSON.quizzes.quiz[id].question[i]._text + "<br>");
				for(j=0; j<quizJSON.quizzes.quiz[id].question[i].option.length; j++) {
					var option = quizJSON.quizzes.quiz[id].question[i].option[j]._text;
					$("#quizForm").append("<input type='checkbox' name='quiz"+(id+1)+" value='"+option+"'/>"+option+"<br>");
				}
				$("#quizForm").append("<br>");
			}
			
			$("#quizForm").append('<input type="hidden" name="action" value="quiz_submit" /><input type="submit" value="Submit" id="submit"name="submit">');

		}
	}
	
	$('#quizForm').submit(function() {
		var post_data = $('#quizForm').serialize();
			$.post('Quiz/quiz_submit.php', post_data, function(data) {
		});
		$('#quizModal').hide();
		$('.modal-backdrop').hide();
	  return false;
	});

</script>