<?php
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

?>
<h1>New Question</h1>
<form id="frm-saved-quiz" method="post">

	<div class="form-group">
    	<label for="question">Question: </label>
    	<textarea name="question" class="form-control quiz_choice" rows="5" id="questnion" required=""></textarea>
  	</div>
  	<div class="form-group">
    	<label for="choice_1">Choice 1: </label>
    	<textarea name="choice_1" class="form-control quiz_choice" id="choice_1" required=""></textarea>
  	</div>
  	<div class="form-group">
    	<label for="choice_2">Choice 2: </label>
    	<textarea name="choice_2" class="form-control quiz_choice" id="choice_2" required=""></textarea>
  	</div>
  	<div class="form-group">
    	<label for="choice_3">Choice 3: </label>
    	<textarea name="choice_3" class="form-control quiz_choice" id="choice_3" required=""></textarea>
  	</div>
  	<div class="form-group">
    	<label for="choice_4">Choice 4: </label>
    	<textarea name="choice_4" class="form-control quiz_choice" id="choice_4" required=""></textarea>
  	</div>
  	<div class="form-group">
    	<label for="answer">Answer: </label>
    	<textarea name="answer" class="form-control" id="answer" required=""></textarea>

  	</div>
  	<?php wp_nonce_field( 'save_mave_quiz', 'save_mave_quiz' ); ?>
  	<div class="form-group">
	
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-success" onclick="jQuery('#frm-saved-quiz').submit();">Save</button>
	</div>
</form>