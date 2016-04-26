<?php
function calcPostQuantities($args,$settings)
{
	global $wpdb;
	$filter = '';

	$dbfilter = get_filter_type($filter,$settings);
	
	//Here we can calculate the # of posts in each one
//	add_filter( 'posts_where', 'azindex_posts_where', 10, 2 );
//	$_REQUEST['azindex'] = '';
	
	//$azindexquerycount = new WP_Query( $args );
	
	$result = array();
	$greying_out = false;
	
	//We can't reliably do this yet
/*	if(strpos($azindexquerycount->request,'wp_term_relationships.term_taxonomy_id') !== FALSE)	//found it
		$greying_out = false;
	else
	{
		//Remove the calc found rows
		if($dbfilter == 'name')
			$sql = str_replace('SQL_CALC_FOUND_ROWS','UPPER('.$wpdb->posts.'.post_'.$dbfilter.') AS Filter',$azindexquerycount->request); 
		else
			$sql = str_replace('SQL_CALC_FOUND_ROWS',$wpdb->posts.'.post_'.$dbfilter.' AS Filter',$azindexquerycount->request); 
		
		//Remove the Post ID - this is causing problems
		$sql = str_replace($wpdb->posts.'.ID','',$sql);

		//Remove the Limit
		$sql = preg_replace ( '/\bORDER BY.+\b/' , '' , $sql  );
		
		$sql .= ' ORDER BY Filter';
		//echo $sql.'<br/>';

		
		remove_filter( 'posts_where', 'azindex_posts_where', 10, 2 );
		
		$result = $wpdb->get_results ( $sql );
	}
*/		
	global $azindex_counts;
	$azindex_counts = array();
	
	$index_filter = '';
	
	if(isset($settings['index']))
		$index_filter = $settings['index'];

	if(isset($_REQUEST['index']))
		$index_filter = $_REQUEST['index'];
		
	if($index_filter != '')
	{
		$tmp = explode(',',$index_filter);
		
		for($i=0;$i<count($tmp);$i++)
		{
			$char = $tmp[$i];
			
			if($greying_out)
				$azindex_counts[$char] = 0;		
			else
				$azindex_counts[$char] = 1;		
		}
	}
	else
	{
		for($i=0;$i<26;$i++)
		{
			$char = chr($i + 65);
			
			if($greying_out)
				$azindex_counts[$char] = 0;		
			else
				$azindex_counts[$char] = 1;		
		}
	}	
	
	if(!$greying_out)
		return;
	
	foreach ( $result as $page )
	{
		$char = substr($page->Filter,0,1);
		
		foreach($azindex_counts as $index => $count)
		{
			if(strlen($index) == 1)	//A simple index
			{
				if($char == $index)
					$azindex_counts[$index]++;
			}
			else                    //A compound index
			{
				$start = ord(substr($index,0,1));
				$end = ord(substr($index,2,1)) + 1;
				
				for($i=$start;$i<$end;$i++)
				{
					if(chr($i) == $char)
						$azindex_counts[$index]++;
				}
			}
		}
	}
}

function getAZIndexContent($settings)
{
	//print_r($settings);
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	$args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'paged' => $paged
	);
		
	if($settings["postcount"] != '')
		$args['showposts'] = $settings["postcount"];
	else
		$args['showposts'] = get_query_var('showposts');
	
	if($settings["category"] != '')
		$args['category_name'] = $settings["category"];

	if($settings["posttype"] != '')
		$args['post_type'] = $settings["posttype"];

	$args['post_title'] = $settings["posttype"];
	
	if($settings['ordering'] != '')
		$args['orderby'] = $settings["ordering"];

	if($settings['order_direction'] != '')
		$args['order'] = $settings["order_direction"];

	$template = 'listing';
	
	if($settings['template'] != '')
		$template = $settings['template'];

	global $azindexquery,$wp_query;
	
	$_REQUEST['content'] = true;

	add_filter( 'posts_where', 'azindex_posts_where', 10, 2 );
	$azindexquery = new WP_Query( $args );
	//echo $azindexquery->request;
	remove_filter( 'posts_where', 'azindex_posts_where', 10, 2 );
	
	calcPostQuantities($args,$settings);
	
	// Pagination fix
	$temp_query = $wp_query;
	$wp_query   = NULL;
	$wp_query   = $azindexquery;	
	
	ob_start();
	
	bemoazindex_load_plugin_template($template);
	
	$retval = ob_get_contents();
	
	ob_end_clean();

	
	// Reset main query object
	$wp_query = NULL;
	$wp_query = $temp_query;	
	
	return $retval;
}


//Load the listing template
function bemoazindex_load_plugin_template( $template = 'listing' )
{
	if ( $overridden_template = locate_template( 'bemo-a-z-index/'.$template . '.php' ) ) 
	{
		//echo "1 Template found at $overridden_template";
	   // locate_template() returns path to file
	   // if either the child theme or the parent theme have overridden the template
	   load_template( $overridden_template );
	 } 
	 else 
	 {
		 $plugin_template_path = dirname( __FILE__ ) . '/templates/'.$template . '.php';
		locate_template( $plugin_template_path );
	   // If neither the child nor parent theme have overridden the template,
	   // we load the template from the 'templates' sub-directory of the directory this file is in
	   $overridden_template = dirname( __FILE__ ) . '/templates/'.$template . '.php' ;
	   
	   //echo "2 Template found at $overridden_template";
	   load_template( $overridden_template );
	 }	
}

?>
