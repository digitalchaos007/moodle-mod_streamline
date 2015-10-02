<?php
	echo $streamline->quiz_xml;
?>

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
	xmlText = <?=json_encode($streamline->quiz_xml)?>;
	console.log("QUIZ");
	console.log(xmlText);
	var quizJSON = x2js.xml_str2json( xmlText );

	if(quizJSON.quizzes.quiz instanceof Array) {
		for(i=0; i< quizJSON.quizzes.quiz.length; i++) {
			var quiz = "<li class='quizOption' data-toggle='modal' data-target='#quizModal' onclick='populateQuiz("+(i)+")'>Quiz "+(i+1)+"</li>";
			$("#quiz_menu").append(quiz);
		}		
	} else {
		console.log("Added drop down");
		i = 0;
		var quiz = "<li class='quizOption' data-toggle='modal' data-target='#quizModal' onclick='populateQuiz("+(i)+")'>Quiz "+(i+1)+"</li>";
		$("#quiz_menu").append(quiz);
	}
	
	function populateQuiz(id) {
	
		$('.modal-title').text("Quiz " + (id+1));
		
		//Check if a quiz with the specified ID exists
		
		//Obtain number of quizzes
		if(quizJSON.quizzes.quiz instanceof Array) {
			number_of_quizzes = quizJSON.quizzes.quiz.length;
			quiz = quizJSON.quizzes.quiz[id];
		} else {
			number_of_quizzes = 1;
			quiz = quizJSON.quizzes.quiz;
		}
				
		if(id <= number_of_quizzes) {
			$("#quizForm").empty();
			
			//Obtain number of questions
			if(quiz.question instanceof Array) {
				number_of_questions = quiz.question.length;
			} else {
				number_of_questions = 1;
			}

			for(i=0; i<number_of_questions; i++) {
				if(number_of_questions == 1) {
					$("#quizForm").append("<b>Question " + (i+1) + "</b> : " + quiz.question._text + "<br>");
					question = quiz.question;
				} else {
					$("#quizForm").append("<b>Question " + (i+1) + "</b> : " + quiz.question[i]._text + "<br>");
					question = quiz.question[i];
				}
				
				//Obtain number of options
				if(question.option instanceof Array) {
					number_of_options = question.option.length;
				} else {
					number_of_options = 1;
				}
				
				for(j=0; j<number_of_options; j++) {
					if(number_of_options == 1) {
						var option = question.option._text;
					} else {
						var option = question.option[j]._text;
					}
					$("#quizForm").append("<input type='checkbox' id='"+(i+1)+"."+(j+1)+"' name='quiz"+(id+1)+" value='"+option+"'/>"+option+"<br>");
				}
				$("#quizForm").append("<br>");
			}
			
			$("#quizForm").append('<input type="hidden" name="action" value="quiz_submit" /><input type="submit" value="Submit" id="submit"name="submit">');

		}
	}
	
	$('#quizForm').submit(function() {
	
		var answerArray = []
		
		var checkboxes = $('input:checkbox');
		
		for(i=0;i<checkboxes.length;i++) {
			if(checkboxes[i].checked) {
				answerArray.push(checkboxes[i].id);
			}
		}
		
		//hey
	
		sid = <?=json_encode($streamline->id)?>;
		stuid = <?=json_encode($USER->id)?>


		
		alert(answerArray);
		
		var post_data = $('#quizForm').serialize();
			$.post('Quiz/quiz_submit.php', post_data, function(data) {
		});
		$('#quizModal').hide();
		$('.modal-backdrop').hide();
		$('body').removeClass( "modal-open" );

	  return false;
	});

</script>