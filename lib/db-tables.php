<?php

/*
* 	Call Global $wpdb
*/

global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$questions_table_name = $wpdb->prefix . 'quiz_questions';
$examinee_table_name = $wpdb->prefix . 'quiz_examinees';


$create_question_table = "CREATE TABLE $questions_table_name (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	question text NOT NULL,
	choice_1 text NOT NULL,
	choice_2 text NOT NULL,
	choice_3 text NOT NULL,
	choice_4 text NOT NULL,
	answer text NOT NULL,
	level text NOT NULL,

	UNIQUE KEY id (id)
) $charset_collate;";


$create_examinee_table = "CREATE TABLE $examinee_table_name (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	name varchar(200) NOT NULL,
	email varchar(200) NOT NULL,
	score integer,
	level text,
	date_taken date,

	UNIQUE KEY id (id)
) $charset_collate;";


require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $create_question_table );
dbDelta( $create_examinee_table );


?>