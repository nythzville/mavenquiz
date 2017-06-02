jQuery.noConflict();
$ = jQuery;
jQuery(document).ready(function ($) {
    // Smart Wizard 	
    $('#wizard').smartWizard({
    	enableAllSteps: false,
    	enableFinishButton: false,
        includeFinishButton: false,
        labelNext: 'Next >>',
        labelPrevious: '<< Previous',
    	onFinish: function(){
           
    	},
        onLeaveStep: function(me, step, context){
            

            /*
            *   Scroll to top of quiz
            */
            $('html, body').animate({
                scrollTop: $('#wizard').offset().top - 100
            }, 'slow');

            
            /*
            *   Going to last step
            */
            if(this.steps.length == step.toStep){
                // Get inputs
                var q_data = get_your_answer();
                if (q_data.error == true) {
                    $('#step-'+ step.toStep + ' #submit-answers').attr("disabled", true);;

                }else{
                    $('#step-'+ step.toStep + ' #submit-answers').removeAttr('disabled');
                }
                $('#step-'+ step.toStep + ' span#q_msg').html(q_data.msg);

                
                var checklist = '';
                checklist += '<li><i class="fa fa-edit text-success"></i> '+q_data.num_answered_questions+'/'+q_data.num_questions+' <strong>Answered</strong> questions.</li>';
                checklist += '<li><i class="fa fa-close text-danger"></i> '+q_data.missed_questions.length+'/'+q_data.num_questions+' <strong>Unaswered</strong> questions.</li>';
                
                // Display question data
                $('#step-'+ step.toStep + ' #result_list').html(checklist);

            }

            return step.toStep;
        }

    });


    /* 
    *	When choice was clicked
    */ 
    $('li.choice').click(function(){
    	var content = $(this).html();
		$(this).closest('.q-row').find('li.choice').removeClass('selected');
    	$(this).addClass('selected');
    	
    	$(this).closest('.q-row').find('span.answer').html(content);
    });

    

});

$('#submit-answers').click(function(){
    /*
    * if Button is disabled then no further actions
    */
    if($(this).attr('disabled')){
        return false;
    }
    /*
    * if Name field has no content then no further actions
    */
    if($("#your-name").val().length < 6){
        $("#your-name").addClass('error-input');
        return false;
    }else{
        $("#your-name").removeClass('error-input');

    }
    if(( $("#your-email").val().length == 0) ||(!IsEmail($("#your-email").val()))){
        $("#your-email").addClass('error-input');
        return false;
    }else{
        
        $("#your-email").removeClass('error-input');

    }
    /*
    *   Get the answer of the examinee
    */
    var answer_data = get_your_answer();    
    if (answer_data == false) { // if no data then no further action
        return false;
    }       
    
    var username = $("#your-name").val();
    var email = $("#your-email").val();

    /*
    *   Structure request data
    */
    var request_data = {
        examinee: {name: username, email: email},
        answer_data: answer_data,
        action: "validate_answers",
        security: Quiz.security
    }
    
    /*
    *   Show loading spinner
    */
    var spinner_content = '<div class="text-center quiz-notif"><i class="fa fa-spin fa-spinner"></i><br/>Evaluating Your Answer...</div>';
    $('.pricing_features').html(spinner_content);

    // Sending ajax post request to server
    $.post(Quiz.ajaxurl, request_data)
    .done(function(response){

        var json_resp = JSON.parse(response);
        console.log(json_resp);
        if (json_resp.error == false) {
            
            $('#score-header').html("Your Score");
            $('#score').html(json_resp.scores.total_score);
            $('#q_msg').html("You are on " + json_resp.level + " level!");

            var response_list = '<ul id="result_list" class="list-unstyled text-left">';
            response_list +='<li><i class="fa fa-check text-success"></i> '+json_resp.scores.total_score+' / '+answer_data.num_questions+' <strong>Correct</strong> answers.</li>';
            // response_list += '<li><i class="fa fa-close text-danger"></i> '+json_resp.mistakes.length+'/'+answer_data.num_questions+' <strong>Wrong</strong> answers.</li>';
            response_list +='<li><i class="fa fa-check text-success"></i> '+json_resp.scores.beginner_score +' <strong>Correct</strong> Beginner Score.</li>';
            response_list +='<li><i class="fa fa-check text-success"></i> '+json_resp.scores.intermediate_score +' <strong>Correct</strong> Intermediate Score.</li>';
            response_list +='<li><i class="fa fa-check text-success"></i> '+json_resp.scores.advance_score +' <strong>Correct</strong> Advance Score.</li>';

            
            response_list += '</ul>';

            $('.pricing_features').html(response_list);
            
        }

    })
    .fail(function(response){
        console.log(response);
    });
});


function get_your_answer(){
	var data = {};
	var answer_data = [];
    var missed_q = [];

    var error = false;
    var msg = "";
    // if number of questions are not equal to number of answer
    if ($('.q-row').length > $('li.selected').length ) {
        // alert("You've miss to answer some questions!");
        error = true;
        msg = "You have missed to answer some questions!";
    }else{
        error = false;
        msg = "Ready to get your score!";
    }

	$('.q-row').each(function(){
		var qid = $(this).attr('id');
		var ans = $(this).find('li.selected').html();
		var qa = {id: qid, ans: ans};
		answer_data.push(qa);

        // if no answer then put it on unaswered collection
        if(( ans == null ) ||( ans== "" )){
            missed_q.push(qa);
        }
	});

	data.num_questions = $('.q-row').length;
    data.num_answered_questions = $('.q-row li.selected').length;
	data.answer_data = answer_data;
    data.error = error;
    data.msg = msg;
    data.missed_questions = missed_q;

	return data;
}

function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!regex.test(email)) {
       return false;
    }else{
       return true;
    }
}