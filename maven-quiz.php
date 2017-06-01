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
	
	// Check / Craete Tables
	require( ABSPATH . 'wp-content/plugins/maven-quiz/lib/db-tables.php');

	add_menu_page('Maven Quiz', 'Maven Quiz', 10, 'maven_quiz', 'maven_quiz', 'dashicons-welcome-write-blog');
	add_submenu_page('maven_quiz', 'New Question', 'New Question', 1, 'new_question', 'new_question' );
	add_submenu_page('maven_quiz', 'Examinee List', 'Examinee List', 1, 'examinee_list', 'examinee_list' );

}

// Show New Question Form
function new_question(){
	wp_enqueue_style( 'css-css', plugins_url( '/maven-quiz/css/quiz-style.css' ));
	wp_enqueue_style('bootstrap-min-css', get_template_directory_uri(). '/bootstrap/css/bootstrap.beautified.css');
	?>
	<h1>New Question</h1>
	<?php if(isset($saved_quiz)){ ?>
	<div id="message" class="updated notice notice-success is-dismissible"><p>Quiz Succesfully saved.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
	<?php } ?>
	<form id="frm-saved-quiz" method="post">
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
					    	<textarea name="question" class="form-control quiz_choice" rows="5" id="questnion" required="" placeholder="Enter question here"></textarea>
					  	</div>
				
					</div>
					<div class="col-md-6">
						<div class="form-group">
					    	<textarea name="choice_1" class="form-control quiz_choice" id="choice_1" required="" placeholder="Enter choice 1 here"></textarea>
					  	</div>
					</div>
					<div class="col-md-6">

					  	<div class="form-group">
					    	<textarea name="choice_2" class="form-control quiz_choice" id="choice_2" required="" placeholder="Enter choice 2 here"></textarea>
					  	</div>
					</div>
					<div class="col-md-6">
					
					  	<div class="form-group">
					    	<textarea name="choice_3" class="form-control quiz_choice" id="choice_3" required="" placeholder="Enter choice 3 here"></textarea>
					  	</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
					    	<textarea name="choice_4" class="form-control quiz_choice" id="choice_4" required="" placeholder="Enter choice 4 here"></textarea>
					  	</div>
					</div>
				</div>
				
			</div>
			<div class="col-md-4">
			  	<div class="form-group">
			    	<textarea name="answer" class="form-control" id="answer" required="" placeholder="Enter answer here"></textarea>
			  	</div>
			  	<div class="form-group">
			    	<select name="level" class="form-control" id="level" required="" placeholder="Choose Level here">
			    		<option value="Beginner">Beginner</option>
			    		<option value="Intermediate">Intermediate</option>
			    		<option value="Advance">Advance</option>

			    	</select>
			  	</div>
			  	<?php wp_nonce_field( 'save_mave_quiz', 'save_mave_quiz' ); ?>
			  	<div class="form-group">
					<button type="submit" class="btn btn-success" >Save Question</button>
				</div>

			</div>
		</div>
		
	  	
	</form>
	<?php
}

function examinee_list(){

	wp_enqueue_style( 'css-css', plugins_url( '/maven-quiz/css/quiz-style.css' ));
	wp_enqueue_style('bootstrap-min-css', get_template_directory_uri(). '/bootstrap/css/bootstrap.beautified.css');

	/*
	*	List table for listing Questions 
	*/
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

	}
	require( ABSPATH . 'wp-content/plugins/maven-quiz/lib/Examinee-list.php' );
	$examinee_list_table =  new Examinee_List();
	$examinee_list_table->prepare_items();
	?>
	<div class="wrap">
		<div class="row">
			<div class="col-md-12">
			<?php if(isset($saved_quiz)){ ?>
				<div id="message" class="updated notice notice-success is-dismissible"><p>Examinee Succesfully saved.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
				<?php }elseif (isset($deleted_quiz)) { ?>
					<div id="message" class="updated notice notice-success is-dismissible"><p>Examinee Succesfully saved.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
				<?php } ?>
				<h2 class="wp-heading-inline">Examinee List</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-md-5">
			
			</div>
			<div class="col-md-3">
				<form method="post">
				  	<input type="hidden" name="page" value="my_list_test" />
				  	<?php $examinee_list_table->search_box('search', 'search_id'); ?>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<form id="question-list" method="post">
					<?php
					$examinee_list_table->display();

					?>
				</form>
				<br class="clear">

			</div>
		</div>
	</div>
<?php
}	


function maven_quiz(){

	wp_enqueue_style( 'css-css', plugins_url( '/maven-quiz/css/quiz-style.css' ));
	wp_enqueue_style('bootstrap-min-css', get_template_directory_uri(). '/bootstrap/css/bootstrap.beautified.css');

	/*
	*	List table for listing Questions 
	*/
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

	}
	require( ABSPATH . 'wp-content/plugins/maven-quiz/Question_List.php' );

	global $wpdb;
	
	$total_q = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}quiz_questions");
	$total_beginner_q = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}quiz_questions WHERE level = 'Beginner'");
	$total_intermediate_q = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}quiz_questions WHERE level = 'Intermediate'");
	$total_advance_q = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}quiz_questions  WHERE level = 'Advance'");

	$question_list_table =  new Question_List();
	$question_list_table->prepare_items();
	?>
	<div class="wrap">
		<div class="row">
			<div class="col-md-12">
			<?php if(isset($saved_quiz)){ ?>
				<div id="message" class="updated notice notice-success is-dismissible"><p>Question Succesfully saved.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
				<?php }elseif (isset($deleted_quiz)) { ?>
					<div id="message" class="updated notice notice-success is-dismissible"><p>Question Succesfully saved.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
				<?php } ?>
				<h2 class="wp-heading-inline">Question List</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-md-5">
				<a href="<?php echo admin_url('/admin.php?page=new_question'); ?>" class="page-title-action">Add Question</a>
				
			</div>
			<div class="col-md-3">
				<form method="post">
				  	<input type="hidden" name="page" value="my_list_test" />
				  	<?php $question_list_table->search_box('search', 'search_id'); ?>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<form id="question-list" method="post">
					<?php
					$question_list_table->display();

					?>
				</form>
				<br class="clear">
			</div>	
			<div class="col-md-4">
				
			<div class="animated flipInY col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-comments-o"></i>
                    </div>
                    <div class="count"><?php echo $total_q ?></div>

                    <h3>Total Questions</h3>
                    <p>Number of all questions in all difficulty level</p>
                </div>
            </div>
            <div class="animated flipInY col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-comments-o"></i>
                    </div>
                    <div class="count"><?php echo $total_beginner_q ?></div>

                    <h3>Beginner Questions</h3>
                    <p>Lowest level of difficulty</p>
                </div>
            </div>
            <div class="animated flipInY col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-comments-o"></i>
                    </div>
                    <div class="count"><?php echo $total_intermediate_q ?></div>

                    <h3>Intermediate Questions</h3>
                    <p>Moderate level of difficulty</p>
                </div>
            </div>
            <div class="animated flipInY col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-comments-o"></i>
                    </div>
                    <div class="count"><?php echo $total_advance_q ?></div>

                    <h3>Advance Questions</h3>
                    <p>Highest level of difficulty</p>
                </div>
            </div>
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
					$your_answer = str_replace('\\', '', $q_item->answer);

					if ($your_answer === $correct_ans->answer) {
						
						$score++;
					}else{
						$q = (object) array('id' => $q_item->id, 'your_answer' => $your_answer, 'correct' => $correct_ans->answer);
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
	$sql = "SELECT * FROM {$wpdb->prefix}quiz_questions ORDER BY RAND() LIMIT 100";
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
                            <h2 id="score-header">Get Your Score</h2>
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
	                                <i>Fill up this form to get your score.</i>
	                                	                                
	                                </p>

	                                <div class="form-group">
                                		<input type="text" class="form-control" name="name" placeholder="Your Name">
                                	</div>
                                	<div class="form-group">
                                		<input type="email" class="form-control" name="email" placeholder="Your Email">
                                	</div>
                                </div>

                            </div>
                            <div class="pricing_footer">
                                <a id="submit-answers" class="btn btn-block" role="button">
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
