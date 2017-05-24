jQuery.noConflict();
$ = jQuery;
jQuery(document).ready(function ($) {
    // Smart Wizard 	
    $('#wizard').smartWizard({
    	enableAllSteps: false,
    	enableFinishButton: true,
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
            
            // if (step.fromStep < step.toStep) {
            //     var cur_step = step.fromStep;
            //     console.log('current step', step.fromStep);
            //     var q_nums = $('#step-' + cur_step +' .q-row .choice_list').length;
            //     var sel_nums = $('#step-' + cur_step +' .q-row .choice_list li.selected').length;
            //     if( q_nums != sel_nums){
            //         return false;
            //     }    
            // }
            
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


function get_your_answer(){
	var data = {};
	var answer_data = [];

    // if number of questions are not equal to number of answer
    if ($('.q-row').length != $('li.selected').length ) {
        alert("You've miss to answer some questions!");
        return false;
    }

	$('.q-row').each(function(){
		var qid = $(this).attr('id');
		var ans = $(this).find('li.selected').html();
		var qa = {id: qid, ans: ans};
		answer_data.push(qa);
	});

	data.num_questions = $('.q-row').length;
	data.answer_data = answer_data;
	// console.log(answer_data);
	return data;
}