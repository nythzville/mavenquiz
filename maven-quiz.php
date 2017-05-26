<?php
  /*
    Plugin Name:  Maven Quiz Plugin
    Plugin URI: http://localhost/
    Description: Plugin for english quiz
    Author: nythzville
    Version: 1.0
    Author URI: http://localhost/
    */

add_action('admin_menu', 'Maven_Quiz_Plugin');

function Maven_Quiz_Plugin() {
	add_menu_page('Maven Quiz', 'Maven Quiz', 10, 'maven_quiz', 'maven_quiz', 'dashicons-welcome-write-blog');
}

function maven_quiz(){

	/*
	* 	Call Global $wpdb
	*/

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'quiz_questions';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		question text  NOT NULL,
		choice_1 text NOT NULL,
		choice_2 text NOT NULL,
		choice_3 text NOT NULL,
		choice_4 text NOT NULL,
		answer text NOT NULL,
		level text NOT NULL,

		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	// $wpdb->query("INSERT INTO ".$wpdb->prefix . "quiz_questions
 //            (`id`, `question`, `choice_1`, `choice_2`, `choice_3`, `choice_4`, `answer`)
 //            VALUES
 //            (1, 'Tom <blank> English', 'are', 'am', 'is', 'be', 'is'),
 //            (2, '<blank> there a restaurant near hear?', 'Are', 'Have', 'Do', 'Is', 'Is'),
 //            (3, 'I didn't <blank> TV last night,  'not watched', 'watch', 'watching', 'watched', 'watched')");


	/*
	*	List table for listing Questions 
	*/
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

	}
	require( ABSPATH . 'wp-content/plugins/maven-quiz/Question_List.php' );

	?>

	<div class="wrap">
		<?php if(isset($saved_quiz)){ ?>
		<div id="message" class="updated notice notice-success is-dismissible"><p>Quiz Succesfully saved.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
		<?php }elseif (isset($deleted_quiz)) { ?>
			<div id="message" class="updated notice notice-success is-dismissible"><p>Quiz Succesfully saved.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
		<?php } ?>
		<h2 class="wp-heading-inline">Quiztion List</h2>
		<!-- <a href="#" class="page-title-action">Add Question</a> -->
		<button type="button" class="btn btn-info btn-lg page-title-action" data-toggle="modal" data-target="#newQuestion">New Question</button>	
		<!-- <button id="btn-new-question" type="button" class="btn btn-info btn-lg page-title-action">New Question</button>	 -->

			<form id="question-list" method="post">
			<?php
			
			$customers =  new Question_List();
			$customers->prepare_items();
			$customers->display();

			?>
			<br class="clear">
		</div>
	</div>


	<!-- Modal -->
	<div id="newQuestion" class="modal fade" role="dialog">
		<div class="modal-dialog">

		    <!-- Modal content -->
		    <div class="modal-content">
		    		
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal">&times;</button>
	        		<h4 class="modal-title">New Question</h4>
	      		</div>
			    <form id="frm-saved-quiz" method="post">

	      		<div class="modal-body">

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
		  
			    </div>

		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        	<button type="button" class="btn btn-success" onclick="jQuery('#frm-saved-quiz').submit();">Save</button>

      			</div>
      			</form>
      			<script type="text/javascript">
      				jQuery(document).ready(function($){

      				});
      			</script>
	    	</div>

		</div>
	</div>

	<?php

}

/*
*	Saving Quiz
*/
add_action('init', 'save_quiz');
function save_quiz(){
	if(isset($_REQUEST['save_mave_quiz']) || wp_verify_nonce( $_REQUEST['save_mave_quiz'], 'save_mave_quiz' )) {
		
		global $wpdb;

		$question = $_REQUEST['question'];
		$choice_1 = $_REQUEST['choice_1'];
		$choice_2 = $_REQUEST['choice_2'];
		$choice_3 = $_REQUEST['choice_3'];
		$choice_4 = $_REQUEST['choice_4'];
		$answer = $_REQUEST['answer'];
		
		if( !empty($question) & !empty($choice_1) & !empty($choice_2) & !empty($choice_3) & !empty($choice_4) & !empty($answer)){
			
			$raw_question = array('question' => $question,
								'choice_1' => $choice_1,
								'choice_2' => $choice_2,
								'choice_3' => $choice_3,
								'choice_4' => $choice_4,
								'answer' => $answer
								);

			$wpdb->insert($wpdb->prefix . 'quiz_questions' , $raw_question);

			$messages['saved_quiz'][2] = 'Quiz successfully saved!';
		    return $messages;
		}

	}	
}

/*
*	Validating Quiz answers
*/
add_action('wp_ajax_validate_answers', 'validate_answers');
add_action('wp_ajax_nopriv_validate_answers', 'validate_answers');
function validate_answers(){
	if(!isset($_REQUEST['security']) || !wp_verify_nonce( $_REQUEST['security'], 'secured-maven-quiz' )) {
		return json_encode(array('error' => true, 'msg' => 'invalid security code!' ));
	}else{

		global $wpdb;

		// Get the corrent answer in database
		$sql = "SELECT id,answer FROM {$wpdb->prefix}quiz_questions";
		$answers_key = $wpdb->get_results($sql);

		$ans_list = $_REQUEST['answer_data']['answer_data'];
		
		$items = array();
		foreach ($ans_list as $answer) {
			$id = $answer["id"];
			$id = intval(str_replace('mq-', '', $id));
			$item = (object) array( 'id' => $id, 'answer' => $answer["ans"] );
			array_push($items, $item);
		}

		/** Compare given items to answer key and get score**/
		$score = 0;
		$mistakes = array();
		foreach ($answers_key as $correct_ans) {
			
			foreach ($items as $q_item) {
				
				if($q_item->id == $correct_ans->id){
					$answer = str_replace('\\', '', $q_item->answer);

					if ($answer === $correct_ans->answer) {
						
						$score++;
					}else{
						$q = (object) array('id' => $q_item->id, 'your-answer' => $answers, 'correct' => $correct_ans->answer);
						array_push($mistakes, $q);
					}

					break;
				}
			}
		}

		echo json_encode(array('error'=> false, 'score' => $score, 'mistakes' => $mistakes));
	}
	exit();
}

/*
* Show Quiz on front end
*/

add_shortcode('show_quiz', 'quiz_display');
function quiz_display($atts){

	global $wpdb;
	wp_enqueue_style( 'css-css', plugins_url( '/maven-quiz/css/quiz-style.css' ));
	wp_enqueue_style( 'font-awesome-min-css', plugins_url( '/maven-quiz/font-awesome/css/font-awesome.min.css' ));

	wp_enqueue_script( 'wizard-form', plugins_url( '/maven-quiz/js/wizard/jquery.smartWizard.js' ));
	wp_enqueue_script( 'bootbox-min-js', plugins_url( '/maven-quiz/js/bootbox.min.js' ));

	wp_enqueue_script( 'quiz-js', plugins_url( '/maven-quiz/js/quiz.js' ));

	// Ajax saving Scripts
	wp_localize_script( 'quiz-js', 'Quiz', array(
	// URL to wp-admin/admin-ajax.php to process the request
	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	// generate a nonce with a unique ID "myajax-post-comment-nonce"
	// so that you can check it later when an AJAX request is sent
	'security' => wp_create_nonce( 'secured-maven-quiz' )));

	$atts = shortcode_atts( array(
        'foo' => 'no foo',
        'baz' => 'default baz'
    ), $atts, 'bartag' );

	/* Get all questions on database */
	$sql = "SELECT * FROM {$wpdb->prefix}quiz_questions ORDER BY RAND()";
	$question_list = $wpdb->get_results( $sql, 'ARRAY_A' );
	$rows = $question_list->num_rows;
	
	?>
	<!-- Smart Wizard -->
	<h1>Test Your skills in English</h1>
    <p>Test your English. This is a quick English test.
	</p>
    <form class="form-horizontal">
    	<!-- Wizard starts -->
    	<div id="wizard" class="form_wizard wizard_horizontal">
        	<ul class="wizard_steps">
    		<?php

    			// Get number of pages 
	    		$pages_no = (count($question_list) / 10);

	    		// if number of items has a remainder then add 1 page
	    		if( (count($question_list) % 10)!= 0 ){
	    			$pages_no++;
	    		}

	    		// Add page indicator links
	    		for ($i= 1; $i <= $pages_no ; $i++) { 
	    		?>
	            <li>
	                <a href="#step-<?php echo $i; ?>">
	                    <span class="step_no"><?php echo $i; ?></span>
	                    <span class="step_descr">
			            <small>Page <?php echo $i; ?></small>
				        </span>
	                </a>
	            </li>
	            <?php } ?>
	            <!-- / Final Step -->
	            <li>
	                <a href="#step-<?php echo (intval($pages_no) + 1); ?>">
	                    <span class="step_no"><i class="fa fa-check"></i></span>
	                    <span class="step_descr">
			            <small>Final Step</small>
				        </span>
	                </a>
	            </li>
	            <!-- / Final Step -->
	        </ul>
           	<?php
    		// List all Question
    		$q_count 	= 0;
    		$page 		= 0;
    		$new_page 	= false;

            foreach ($question_list as $question) {
            	
            	if (($q_count == 0) || (($q_count >= 10) && ($q_count % 10) == 0)) { // if count has modulo every 5 counts
            		$new_page = true; // adding page will be true
            	}

            	$q_count++;	// adding questions counts

            	if ($new_page == true) {
            		$page++;

            		if ($q_count != 1) {
            			echo '</div>';
            		}
            		echo '<div id="step-'.$page.'">';
            		$new_page = false;
            	}
	           	?>
	            <div id="mq-<?php echo $question['id']; ?>" class="form-group q-row">
		            <?php
		            $question_display = $question['question'];
		            $question_display = str_replace('<blank>', '<span class="answer">__</span>', $question_display);
	            	?>
	                <div class="col-md-12 col-sm-12 col-xs-12">
		                <p class="question"><?php echo $q_count; ?>. <?php echo $question_display; ?></p>
	                </div>
	                <div class="col-md-12 col-sm-12 col-xs-12">
		                <ul class="choice_list">
		                	<li class="choice"><?php echo $question['choice_1']; ?></li>
		                	<li class="choice"><?php echo $question['choice_2']; ?></li>
		                	<li class="choice"><?php echo $question['choice_3']; ?></li>
		                	<li class="choice"><?php echo $question['choice_4']; ?></li>
		                </ul>
	                </div>
	            </div>
	            <?php 
	            if ( count($question_list) == $q_count) {
            		echo '</div>';
            	}
	        }
         	?>    
		    <div id="step-<?php echo (intval($pages_no) + 1); ?>" class="form-group">
		     	<div class="col-md-offset-4 col-md-4">
                    <div class="pricing">
                        <div class="title">
                            <h2>Get Your Score</h2>
                            <h1 id="score">--</h1>
                            <span id="q_msg">-----</span>
                        </div>
                        <div class="x_content">
                            <div class="">
                                <div class="pricing_features">
                                    <ul id="result_list" class="list-unstyled text-left">
                                        <li><i class="fa fa-edit text-success"></i> 17/20 <strong>Andvance</strong> questions</li>
                                        <li><i class="fa fa-edit text-success"></i> 35/40 <strong>Intermediate</strong> questions</li>
                                        <li><i class="fa fa-edit text-success"></i> 40/40 <strong>Beginner</strong> questions</li>
                                    </ul>
	                                <p style="margin-top: 50px; ">
	                                <strong>Note:</strong> <i>All questions must have answers before submitting.</i>
	                                </p>
                                </div>

                            </div>
                            <div class="pricing_footer">
                                <a id="submit-answers" class="btn btn-success btn-block" role="button">
                                 Submit</a>
                                
                            </div>
                        </div>
                    </div>
                </div>	
		    </div>       
	    </div>
    </form>    
    <!-- End SmartWizard Content -->
	<?php
}
