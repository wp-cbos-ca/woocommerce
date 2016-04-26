<?php


//$azindex_shortcode_settings = array();
/*
Plugin Name: BEMO A-Z Index
Plugin URI: http://www.bemoore.com/bemo-a-z-index-pro/
Description: This is a simple plugin that provides an A-Z index of the posts displayed on a particular page based on the post title.
Version: 1.1.19
Author: Bob Moore (BeMoore Software)
Author URI: http://www.bemoore.com
License: GPL2
*/

/*  
Copyright 2013-2016  Bob Moore  (email : bob.moore@bemoore.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* TODO: 
 * 1. When a static page is set as the homepage, and the index is placed on it with no permalink,
 * it seems not to work.
*/

function azindex_init() 
{
   /* Register our stylesheet. */
   wp_register_style( 'azindex_stylesheet', plugins_url('bemoazindex.css', __FILE__) );
   wp_enqueue_style('azindex_stylesheet');
}
add_action('wp_enqueue_scripts', 'azindex_init');   

function azindex($atts)
{
	if(is_admin())
		return;
	
	return azindex_get_index($atts);
}

add_shortcode('azindex', 'azindex');


function azindex_get_simple_index($azindex,$settings)
{
	global 	$azindex_counts;
	$retval = '';
	
	if($settings['prefix'] != null)
		$retval .= '<div>'.$settings['prefix'].'</div>';
		
	$clean_url = get_cleaned_url();
		
	for($i=0;$i<26;$i++)
	{
		$letter[$i] = chr($i + 65);
		$href = $clean_url;

		$settings['azindex'] = $letter[$i];
		$href = add_query_arg( $settings, $href );
		
		$href = fix_category_link($href,$settings);
		
		if($azindex == "")	//Not selected -> link
		{
			$letter = $letter[$i];
			if(is_array($azindex_counts) && $azindex_counts[$letter] == 0)
				$retval .= '<div class="empty" >'.$letter.'</div>';
			else
				$retval .= '<div><a href="'.$href.'">'.$letter.'</a></div>';
		}
		else if($azindex == $letter[$i])
			$retval .= '<div class="selected" >'.$letter[$i].'</div>';
		else
		{
			$letter = $letter[$i];
			if(is_array($azindex_counts) && $azindex_counts[$letter] == 0)
				$retval .= '<div class="empty" >'.$letter.'</div>';
			else
				$retval .= '<div><a href="'.$href.'">'.$letter.'</a></div>';
		}
	}
	
	return $retval;
}

function azindex_get_predefined_index($azindex,$settings)
{
	global 	$azindex_counts;
	$retval = '';
	$indexes = explode(",",$settings['index']);

	$clean_url = get_cleaned_url();
	
	if($settings['prefix'] != null)
		$retval .= '<div>'.$settings['prefix'].'</div>';	

	for($i=0;$i<count($indexes);$i++)
	{
		$href = $clean_url;

		$settings['azindex'] = $indexes[$i];

		$href = add_query_arg( $settings , $href);
		$href = fix_category_link($href,$settings);
		
		if($azindex == "")	//Not selected -> link
		{
			$selected_index = $indexes[$i];

			if(is_array($azindex_counts) && $azindex_counts[$selected_index] == 0)
				$retval .= '<div class="empty" >'.$selected_index.'</div>';
			else
				$retval .= '<div><a href="'.$href.'">'.$selected_index.'</a></div>';
 			
		}
		else if($azindex == $indexes[$i])
			$retval .= '<div class="selected" >'.$indexes[$i].'</div>';
		else
		{
			$selected_index = $indexes[$i];

			if(is_array($azindex_counts) && $azindex_counts[$selected_index] == 0)
				$retval .= '<div class="empty" >'.$selected_index.'</div>';
			else
				$retval .= '<div><a href="'.$href.'">'.$selected_index.'</a></div>';
		}
	}
	
	return $retval;
}	

function get_cleaned_url()
{
	$current_page_url = site_url().$_SERVER['REQUEST_URI'];	

	//Remove /page/xx from URL
	$cleaned_url = preg_replace ( '/\b\/page\/([0-9]+)?\b/' , '' , $current_page_url  );
	
	return $cleaned_url;
}

function fix_category_link($href,$settings)
{	
	//This is a fix if there is a category selected and we are in a single post
	$post_type = get_post_type( get_the_ID() );
	
	if(isset($settings['category']) && ($post_type == 'post' || $post_type == $settings['posttype']))
	{
		//echo 'Category is '.$settings['category'].' for this post, so we need to redirect';
		// Get term by name ''news'' in Categories taxonomy.
		$category_obj = get_term_by('name', $settings['category'], 'category');
		
		$category_slug = '/'.$category_obj->slug.'/';
		
		global $post;
		$post_slug = '/' . $post->post_name.'/';	

		$href = site_url().$category_slug.$href;
	}		
		
	return $href;
}

function azindex_get_index($atts)
{
	$retval = '';
	
	$settings = array();
	
	$settings['filter'] = isset($atts['filter']) ? $atts['filter'] : 'title';
	$settings['debug'] = isset($atts['debug']) ? $atts['debug'] : null;
	$settings['target'] = isset($atts['target']) ? $atts['target'] : null;
	$settings['index'] = isset($atts['index']) ? $atts['index'] : null;
	$settings['content'] = isset($atts['content']) ? $atts['content'] : null;
	
	$settings['all'] = isset($atts['all']) ? $atts['all'] : 'ALL';
	$settings['prefix'] = isset($atts['prefix']) ? $atts['prefix'] : null;
	$settings['suffix'] = isset($atts['suffix']) ? $atts['suffix'] : null;	

	$settings['posttype'] = isset($atts['posttype']) ? $atts['posttype'] : null;
	$settings['category'] = isset($atts['category']) ? $atts['category'] : null;
	$settings['template'] = isset($atts['template']) ? $atts['template'] : null;
	$settings['postcount'] = isset($atts['postcount']) ? $atts['postcount'] : null;
	$settings['ordering'] = isset($atts['ordering']) ? $atts['ordering'] : null;
	

	
	$settings['order_direction'] = isset($atts['direction']) ? $atts['direction'] : null;
	
	$azindex = get_query_var('azindex');
	
	$retval .= '<div class="bemoazindex" >';

	$content = '';
	
	if($settings['content']	 == "true")
	{
		require_once("bemoazindex_content.php");
			
		$settings['azindex'] = $azindex;
		$content = getAZIndexContent($settings);
		
		global 	$azindex_counts;
	}
	
	if($settings['index'] == '')
		$retval .= azindex_get_simple_index($azindex,$settings);
	else
		$retval .= azindex_get_predefined_index($azindex,$settings);

	
	if($azindex == "")	//Not selected -> link
		$retval .= '<div>'.$settings['all'] .'</div>';
	else
	{
		$href = $_SERVER["REQUEST_URI"] ;
		$href = remove_query_arg('azindex',$href);
		$href = remove_query_arg($settings,$href);
		
		$retval .= '<div><a href="'.$href.'">'.$settings['all'] .'</a></div>';
	}
	
	if($settings['suffix'] != null)
		$retval .= '<div>'.$settings['suffix'].'</div>';		
	
	$retval .= '</div>';
	
    $license_key_field_name = 'azindex_license_key';

    // Read in existing option value from database
    $license_key_val = get_option( $license_key_field_name );

	$stringtocompare = "bemoazindex". $_SERVER['SERVER_NAME'] ."bemoazindex";
	$stringtocomparedev = "bemoazindexdeveloperbemoazindex";
	
    if(md5($stringtocompare) != $license_key_val && md5($stringtocomparedev) != $license_key_val)
    {
		if ( current_user_can( 'manage_options' )  )
			$retval .= '<p>Unlicensed - click <a href="'.get_site_url().'/wp-admin/options-general.php?page=azindex_settings">Here</a> to license  </p>';
		else
			$retval .= '<p><a style="display: block" href="http://www.bemoore.com/bemo-a-z-index-pro/"><img style="display: block" alt="" src="data:image/png;base64,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" /></a></div></p>';
	}

	if($content != '')
		$retval .= $content;

	return $retval;
}

function add_bemoazindex_query_vars( $vars )
{
  $vars[] = "azindex";
  $vars[] = "filter";
  $vars[] = "target";
  $vars[] = "debug";
  $vars[] = "content";
  $vars[] = "all";
  $vars[] = "ordering";
  $vars[] = "prefix";
  $vars[] = "suffix";
//  $vars[] = "ignoreprefixes";
  return $vars;
}
add_filter( 'query_vars', 'add_bemoazindex_query_vars',10,1 );

add_filter('posts_where','azindex_posts_where',10,2);

function get_filter_type(&$filter,$settings = '')
{
	$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';	
	
	if($filter == '')
		$filter = isset($settings['filter']) ? $settings['filter'] : 'title';	
	
	if($filter == 'slug')
		return 'name';
	else
		return $filter;
}

function azindex_posts_where( $where , &$wp_query ) 
{
	global $wpdb;
	
	$filter = '';
	//Have to use $_REQUEST as get_query_var isn't reliable
	$azindex = isset($_REQUEST['azindex']) ? $_REQUEST['azindex'] : '';
	$dbfilter = get_filter_type($filter);
	$debug = isset($_REQUEST['debug']) ? true : false;
	$content = isset($_REQUEST['content']) ? true : false;
	$all = isset($_REQUEST['all']) ? $_REQUEST['all'] : "ALL";
	$prefix = isset($_REQUEST['prefix']) ? $_REQUEST['prefix'] : null;
	$suffix = isset($_REQUEST['suffix']) ? $_REQUEST['suffix'] : null;

//	$ignoreprefixes = isset($_REQUEST['ignoreprefixes']) ? $_REQUEST['ignoreprefixes'] : '';

	$target = isset($_REQUEST['target']) ? (int)$_REQUEST['target'] : 2;
	
	//We are generating the content ourselves - no need to change anything here
	
	$id_string = $wpdb->posts.".ID";
	
	if(strpos($where,$id_string) === false )
	{
		if($azindex != "")
		{
			static $counter = 0;
			++$counter;		
			
			if($debug)
			{
				echo '<pre>';
				echo "Target : ".$counter;
			}			
				
			if($target == $counter)
			{	
				if(strlen($azindex) == 1)
					$where .= " AND {$wpdb->posts}.post_".$dbfilter." LIKE '".esc_sql($azindex)."%'";
				else if(strlen($azindex) == 3)
					$where .= " AND {$wpdb->posts}.post_".$dbfilter." REGEXP '^[".esc_sql($azindex)."]'";

				if($debug)
				{
					echo " : Filter Active : Filtering $azindex by $filter ";
				}
			}
			else
			{
				if($debug)
					echo " : Filter Not Active";
			}
			
			if($debug)
			{			
				echo '</pre>';
			}

		}
		
	}
	
	//echo $where;
	//die();

	return $where;
}

//Init the options
include ('bemoazindex_options.php');

//Init the admin category stuff
include ('bemoazindex_category.php');

//Init the custom post type
include ('bemoazindex_custom_posts.php');

//Include the form for TinyMCE
include ('bemoazindex_form.php');
?>
