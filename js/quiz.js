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
            
            // 
    		var answer_data = get_your_answer();
            
            if (answer_data == false) {
                return false;
            }  		

    		var request_data = {

    			answer_data: answer_data,
    			action: "validate_answers",
    			security: Quiz.security
    		}

            bootbox.dialog({ closeButton: true, message: '<div class="text-center quiz-notif"><i class="fa fa-spin fa-spinner"></i> Loading...</div>' });           
            console.log(answer_data);
            // Sending ajax post request to server
    		$.post(Quiz.ajaxurl, request_data)
    		.done(function(response){

                var json_resp = JSON.parse(response);
    			console.log(json_resp);
                if (json_resp.error == false) {
                    
                    var notif_content = '<div class="m-quiz-notif"><i class="fa fa-check-square-o" aria-hidden="true"></i><p>Your Score is: ' + json_resp.score + '</p></div>';
                    $('.quiz-notif').html(notif_content);

                }

    		})
    		.fail(function(response){
    			console.log(response);
    		});
    	},
        onLeaveStep: function(me, step, context){
            
            /*
            *   
            */

            // if (step.fromStep < step.toStep) {
            //     var cur_step = step.fromStep;
            //     console.log('current step', step.fromStep);
            //     var q_nums = $('#step-' + cur_step +' .q-row .choice_list').length;
            //     var sel_nums = $('#step-' + cur_step +' .q-row .choice_list li.selected').length;
            //     if( q_nums != sel_nums){
            //         return false;
            //     }    
            // }

            /*
            *   Scroll to top of quiz
            */
            $('html, body').animate({
                scrollTop: $('#wizard').offset().top - 100
            }, 'slow');

            
            /*
            *   Going to last step
            */
            console.log(this.steps.length + "=" + step.toStep);
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
    // 

    if($(this).attr('disabled')){
        return false;
    }
    
    var answer_data = get_your_answer();
    
    if (answer_data == false) {
        return false;
    }       

    var request_data = {

        answer_data: answer_data,
        action: "validate_answers",
        security: Quiz.security
    }
    var spinner_content = '<div class="text-center quiz-notif"><i class="fa fa-spin fa-spinner"></i><br/>Evaluating Your Answer...</div>';
    $('.pricing_features').html(spinner_content);

    // console.log("submit");

    // Sending ajax post request to server
    $.post(Quiz.ajaxurl, request_data)
    .done(function(response){

        var json_resp = JSON.parse(response);
        console.log(json_resp);
        if (json_resp.error == false) {
            
            $('#score-header').html("Your Score");
            $('#score').html(json_resp.score);
            $('#q_msg').html("Congratulations!");

            var response_list = '<ul id="result_list" class="list-unstyled text-left">';
            response_list +='<li><i class="fa fa-edit text-success"></i> '+json_resp.score+'/'+answer_data.num_questions+' <strong>Correct</strong> answers.</li>';
            response_list += '<li><i class="fa fa-close text-danger"></i> '+json_resp.mistakes.length+'/'+answer_data.num_questions+' <strong>Wrong</strong> answers.</li>';
            response_list += '</ul>';

            $('.pricing_features').html(response_list);
            // var notif_content = '<div class="m-quiz-notif"><i class="fa fa-check-square-o" aria-hidden="true"></i><p>Your Score is: ' + json_resp.score + '</p></div>';
            // $('.quiz-notif').html(notif_content);

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
    if ($('.q-row').length != $('li.selected').length ) {
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
    data.num_answered_questions = $('li.selected').length;
	data.answer_data = answer_data;
    data.error = error;
    data.msg = msg;
    data.missed_questions = missed_q;

	return data;
}