jQuery.noConflict();
$ = jQuery;
jQuery(document).ready(function ($) {
    // Smart Wizard 	
    $('#wizard').smartWizard({
    	enableAllSteps: false,
    	enableFinishButton: true,
    	onFinish: function(){
			
    		var answer_data = get_your_answer();    		
    		var request_data = {

    			answer_data: answer_data,
    			action: "validate_answers",
    			security: Quiz.security
    		}

    		// var data = "action=validate_answers";
    		// data += "&security=" + Quiz.security;
    		// data += "&answer_data=" + Quiz.security;

    		// console.log('request_data', request_data.serialize());
    		// $.ajax(
    		// 	{	type: 'POST',
    		// 		url: Quiz.ajaxurl,
    		// 		dataType: 'json',
    		// 		data: data,
    		// 		success: function(response){
    		// 			//
    		// 			console.log(response);
    		// 		}
    		// 	}
    		// );
    		$.post(Quiz.ajaxurl, request_data)
    		.done(function(response){
    			console.log(response);
    		})
    		.fail(function(response){
    			console.log(response);
    		});
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