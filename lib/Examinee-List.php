<?php
class Examinee_List extends WP_List_Table {
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Examinee', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Examinees', 'sp' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );
	}

	function get_columns(){
	  $columns = array(
	  	'cb'        => '<input type="checkbox" />',
	    'name'    		=> __('Name', 'sp'),
	    'email'      		=> __('Email','sp'),
	    'score'      		=> __('Score','sp'),
	    'Level'      		=> __('Level','sp')

	  );
	  return $columns;
	}

	function prepare_items() {

		// $items = $this->get_examinees();
	  
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


		  $this->items = self::get_examinees( $per_page, $current_page );
	}


	function column_default( $item, $column_name ) {
	  switch( $column_name ) { 
	    case 'name':
	    case 'email':
	    case 'score':
	    case 'level':

	      return $item[ $column_name ];
	    default:
	      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  }
	}

	public static function get_examinees($per_page = 5, $page_number = 1 ){

		global $wpdb;

		/*
		*	Getting Examinees from wp custom table
		*/

		$sql = "SELECT * FROM {$wpdb->prefix}quiz_examinees";

		$sql .= isset($_POST['s'])? " WHERE examinee LIKE '%".$_POST['s']."%'": "";

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
	    'name' => array('name',true),
	    'score'   => array('answer',false),
	    'level'   => array('answer',false)

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
            '<input type="checkbox" name="examinees[]" value="%s" />', $item['id']
        );    
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
					  	
				    	$delete_ids = esc_sql( $_POST['examinees'] );
					    // loop over the array of record IDs and delete them
					    foreach ( $delete_ids as $id ) {
					      	self::delete_examinee( $id );
					    }
				  		
				  		$messages['deleted_examinee'][2] = 'Examinee successfully deleted!';		    
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

	  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}quiz_examinees";

	  return $wpdb->get_var( $sql );
	}

	/*
	*	Examinee management
	*/
	public static function delete_examinee($id){
		global $wpdb;

		$sql = "DELETE FROM {$wpdb->prefix}quiz_examinees WHERE id = " .$id. "";

		return $wpdb->get_results( $sql );

	}

	
}
?>