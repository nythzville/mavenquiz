jQuery.noConflict();
$ = jQuery;
jQuery(document).ready(function ($) {
    // Smart Wizard 	
    $('#wizard').smartWizard({
    	enableAllSteps: false,
    	enableFinishButton: true,
    	onFinish: function(){
    		// console.log("validate");
    		var data = $(this).closest('form').serialize();
    		data += "&action=validate_answers";
    		data += "&security=" + Quiz.security;
    		console.log(data);

    		$.post(Quiz.ajaxurl, data)
    		.done(function(responseData){
    			console.log(responseData);
    		})
    		.fail(function(responseData){
    			console.log(responseData);

    		});	
    	}

    });


    

});


