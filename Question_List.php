<?php
class Question_List extends WP_List_Table {
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Menu', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Menus', 'sp' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );
	}

	function get_columns(){
	  $columns = array(
	  	'cb'        => '<input type="checkbox" />',
	    'question'    		=> 'Questions',
	    'answer'      		=> 'Answer'

	  );
	  return $columns;
	}

	function prepare_items() {

		// $items = $this->get_questions();
	  	$columns = $this->get_columns();
	  	// $this->process_bulk_action();
	 //  	$current_page = $this->get_pagenum();
	 //  	$total_items  = count($items);
	 //  	$per_page = 10;
	 //  	$this->set_pagination_args( [
		//     'total_items' => $total_items, //WE have to calculate the total number of items
		//     'per_page'    => $per_page //WE have to determine how many items to show on a page
		//   ] );
	  
	  $hidden = array();
	  $sortable = $this->get_sortable_columns();
	  $this->_column_headers = array($columns, $hidden, $sortable);
	 //  $this->items = $items;

		$this->_column_headers = $this->get_column_info();

		  /** Process bulk action */
		  $this->process_bulk_action();

		  $per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
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
	    'title' => array('title',false),
	    'url'   => array('url',false)
	  );
	  return $sortable_columns;
	}

	function get_bulk_actions() {
	  $actions = array(
	    'delete'    => 'Delete'
	  );
	  return $actions;
	}

	function column_title($item) {
	  $actions = array(
	            'edit'      => sprintf('<a href="post.php?post=%s&action=%s">Edit</a>',$item['object_id'],'edit'),
	            'delete'    => sprintf('<a href="post.php?post=%s&action=%s">Delete</a>',$item['object_id'],'delete'),
	            'view'    	=> sprintf('<a href="%s">View</a>',$item['url']),

	        );

	  return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions) );
	}

	function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="menu[]" value="%s" />', $item['ID']
        );    
    }
	

	// Bulk Action
	public function process_bulk_action() {

	  //Detect when a bulk action is being triggered...
	  if ( 'delete' === $this->current_action() ) {

	    // In our file that handles the request, verify the nonce.
	    $nonce = esc_attr( $_REQUEST['_wpnonce'] );

	    if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
	      die( 'Go get a life script kiddies' );
	    }
	    else {
	      self::delete_customer( absint( $_GET['customer'] ) );

	      wp_redirect( esc_url( add_query_arg() ) );
	      exit;
	    }

	  }

	  // If the delete bulk action is triggered
	  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
	       || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
	  ) {

	    $delete_ids = esc_sql( $_POST['bulk-delete'] );

	    // loop over the array of record IDs and delete them
	    foreach ( $delete_ids as $id ) {
	      self::delete_customer( $id );

	    }

	    wp_redirect( esc_url( add_query_arg() ) );
	    exit;
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