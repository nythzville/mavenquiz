<?php
class Question_List extends WP_List_Table {
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Question', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Questions', 'sp' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );
	}

	function get_columns(){
	  $columns = array(
	  	'cb'        => '<input type="checkbox" />',
	    'question'    		=> __('Question', 'sp'),
	    'answer'      		=> __('Answer','sp'),
	    'level'      		=> __('Level','sp')


	  );
	  return $columns;
	}

	function prepare_items() {

		// $items = $this->get_questions();
	  
	  	$hidden = array();
	  	$columns = $this->get_columns();

	  	$sortable = $this->get_sortable_columns();

	  	$this->_column_headers = array($columns, $hidden, $sortable);
		// ** Process bulk action */

		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		  $this->set_pagination_args( [
		    'total_items' => $total_items, //WE have to calculate the total number of items
		    'per_page'    => $per_page //WE have to determine how many items to show on a page
		  ] );


		  $this->items = self::get_questions( $per_page, $current_page );
	}


	function column_default( $item, $column_name ) {
	  switch( $column_name ) { 
	    case 'question':
	    case 'answer':
	    case 'level':

	      return $item[ $column_name ];
	    default:
	      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  }
	}

	public static function get_questions($per_page = 5, $page_number = 1 ){

		global $wpdb;

		/*
		*	Getting Questions from wp custom table
		*/

		$sql = "SELECT * FROM {$wpdb->prefix}quiz_questions";

		$sql .= isset($_POST['s'])? " WHERE question LIKE '%".$_POST['s']."%'": "";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
		    $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		    $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;

	}

	// Sorting
	function get_sortable_columns() {
	  $sortable_columns = array(
	    'question' => array('question',false),
	    'answer'   => array('answer',false),
	    'level'   => array('level',true)

	  );
	  return $sortable_columns;
	}

	function get_bulk_actions() {
	  $actions = array(
	    'delete'    => 'Delete'
	  );
	  return $actions;
	}

	function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="questions[]" value="%s" />', $item['id']
        );    
    }
	
	 function column_question($item) {
	  	$actions = array(
	        'edit' => sprintf('<a href="?page=questions&action=%s&question=%s">Edit</a>','edit',$item['id']),
	    );

	  	return sprintf('%1$s %2$s', str_replace('<blank>', '<span class="blank"> __ </span>',$item['question']), $this->row_actions($actions) );
	  	
	}

	// Bulk Action
	public function process_bulk_action() {

	  	//Detect when a bulk action is being triggered...
	  	if ( 'delete' === $this->current_action() ) {

		    // In our file that handles the request, verify the nonce.
		    $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
	        $action = 'bulk-' . $this->_args['plural'];

		    if ( ! wp_verify_nonce(  $nonce, $action ) ) {
		      die( 'Go get a life script kiddies' );
		    }
		    else {

		    	$action = $this->current_action();

			    switch ( $action ) {

			        case 'delete':
			        	// If the delete bulk action is triggered
					  	
				    	$delete_ids = esc_sql( $_POST['questions'] );
					    // loop over the array of record IDs and delete them
					    foreach ( $delete_ids as $id ) {
					      	self::delete_question( $id );
					    }
				  		
				  		$messages['deleted_quiz'][2] = 'Quiz successfully deleted!';		    
			            break;

			        case 'save':
			            wp_die( 'Save something' );
			            break;

			        default:
			            // do nothing or something else
			            return;
			            break;
			    }

		    return $messages;
		    }

		}

	  	
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
	  global $wpdb;

	  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}quiz_questions";

	  return $wpdb->get_var( $sql );
	}

	/*
	*	Question management
	*/
	public static function delete_question($id){
		global $wpdb;

		$sql = "DELETE FROM {$wpdb->prefix}quiz_questions WHERE id = " .$id. "";

		return $wpdb->get_results( $sql );

	}

	// Convert object to array
	function objectToArray($d) 
	{
	    if (is_object($d)) {
	        $d = get_object_vars($d);
	    }

	    if (is_array($d)) {
	
	        return $d;
	    } else {
	        return $d;
	    }
	}
}
?>