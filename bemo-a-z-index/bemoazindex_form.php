<?php
add_action('admin_footer', 'azindex_admin_footer_function');
function azindex_admin_footer_function() {
	
	global $pagenow;
	
	$edit_page = false;
    if( 'post.php' == $pagenow && $_REQUEST['action'] == 'edit' ) 
    {
        echo 'Current page is editing the post';
        $edit_page = true;
    }
    
   if(!$edit_page)
		return;	
?>
<div id="dialog-form" title="Add A-Z Index" style="display: none">
  <form id="azindex_enter">
    <fieldset>
	<div>
      <label for="index">Custom Index</label>
      <input type="text" name="index" id="index" value="" class="text ui-widget-content ui-corner-all">
	</div>
	<div>
      <label for="all">Text for ALL</label>
      <input type="text" name="all" id="all" value="" class="text ui-widget-content ui-corner-all">
	</div>	
	<div>
      <label for="all">Text for prefix</label>
      <input type="text" name="prefix" id="prefix" value="" class="text ui-widget-content ui-corner-all">
	</div>	
	<div>
      <label for="all">Text for suffix</label>
      <input type="text" name="suffix" id="suffix" value="" class="text ui-widget-content ui-corner-all">
	</div>	
	<div>
      <label for="debugging">Debugging</label>
      <input type="checkbox" name="debugging" id="debugging" value="1" class="text ui-widget-content ui-corner-all">
	</div>
	<div class="filter_specific">
	  <label for="filter">Filter</label>
	  <select name="filter" class="ui-widget-content" >
		  <option value="">Default</option>
		  <option value="title">Title</option>
		  <option value="content">Content</option>
		  <option value="excerpt">Excerpt</option>
		  <option value="slug">Slug</option>
	  </select>
	</div>
<!--	
	<div>
	<label for="ignoreprefixes">Ignore Prefixes</label>
      <input type="text" name="ignoreprefixes" id="ignoreprefixes" value="" class="text ui-widget-content ui-corner-all">
	</div>
-->	
	<div>
      <label for="content">Generate Content?</label>
      <input type="checkbox" name="content" id="content" value="1" class="text ui-widget-content ui-corner-all">
	</div>
	<div>
      <label for="target">Target</label>
      <select name="target" id="target" class="ui-widget-content">
		  <option value="">Default</option>
<?php
			for($i=1;$i<21;$i++)
				echo '<option value="'.$i.'">'.$i.'</option>';
?>		  
	  </select>
	</div>
	<div class="content_specific">
      <label for="posttype">Post Type</label>
      <select name="posttype" class="ui-widget-content" >
		  <option value="">All</option>
<?php
			$args = array(
			   'name' => 'property',
			   'public' => true
			);
			
			$categories_dropdown = array();

			$post_types = get_post_types(array('public' => true ), 'objects' );
			foreach ( $post_types as $post_type )
			{
				echo '<option value="'.$post_type->name.'" taxonomies="';
				
				$taxonomy_names = get_object_taxonomies( $post_type->name );
				
				for($i=0;$i<count($taxonomy_names);$i++)
				{
					if($i > 0)
						echo " ";
						
					echo $taxonomy_names[$i];
					
				$cat_args = array(
					'type'                     => $post_type->name,
					'child_of'                 => 0,
					'parent'                   => '',
					'orderby'                  => 'name',
					'order'                    => 'ASC',
					'hide_empty'               => 1,
					'hierarchical'             => 1,
					'exclude'                  => '',
					'include'                  => '',
					'number'                   => '',
					'taxonomy'                 => $taxonomy_names[$i],
					'pad_counts'               => false 
				); 				
				
				$categories = get_categories($cat_args);
				  foreach($categories as $category) 
				  {
						$categories_dropdown[$post_type->name][$category->name]['slug'] = $category->slug; 	  
						$categories_dropdown[$post_type->name][$category->name]['taxonomy'] = $category->taxonomy; 	  
				  }
						
				}
				
				echo '" >'.$post_type->name.'</option>';		

			}
?>		  
	</select>
	</div>
	<div class="content_specific">
      <label for="categories">Categories</label>
      <select name="categories" class="ui-widget-content" >
		  <option value="">Default</option>
<?php
		foreach($categories_dropdown as $post_type => $vars)
		{
			foreach($vars as $catname => $catdetails)
				echo '<option value="'.$catdetails['slug'].'" taxonomy="'.$catdetails['taxonomy'].'" class="category_options" >'.$catname .'</option>';
		}
?>		  
	</select>
	</div>
	<div class="content_specific">
      <label for="postcount">Posts Per Page</label>
      <select name="postcount" class="ui-widget-content">
		  <option value="">Default</option>
<?php
			for($i=1;$i<51;$i++)
				echo '<option value="'.$i.'">'.$i.'</option>';
?>		  
	  </select>
	</div>
	<div class="content_specific">
      <label for="template">Template</label>
      <input type="text" name="template" id="template" value="" class="text ui-widget-content ui-corner-all">
	</div>
	<div  class="content_specific">
      <label for="filter">Order By</label>
      <select name="ordering" class="ui-widget-content" >
		  <option value="">Default</option>
		  <option value="title">Title</option>
		  <option value="content">Content</option>
		  <option value="excerpt">Excerpt</option>
		  <option value="slug">Slug</option>
	  </select>
	</div>

	<div  class="content_specific">
      <label for="direction">Direction</label>
      <select name="direction" class="ui-widget-content" >
		  <option value="">Default</option>
		  <option value="ASC">Ascending (A -> Z)</option>
		  <option value="DESC">Descending (Z -> A)</option>
	  </select>
	</div>	
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
<?php
}

/* Register buttons */
add_action('init', 'azindex_add_button');

function azindex_add_button() {
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
   {
     add_filter('mce_external_plugins', 'azindex_add_plugin');
     add_filter('mce_buttons', 'azindex_register_button');
   }
}

function azindex_register_button($buttons) {
   array_push($buttons, "azindex");
   return $buttons;
}

function azindex_add_plugin($plugin_array) {
   $plugin_array['azindex'] = plugins_url('js/bemoazindex.js', __FILE__ );
   return $plugin_array;
}

function azindex_admin_scripts_and_styles()
{
	/* Jquery UI Dialog*/
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style("wp-jquery-ui-dialog");

   wp_register_style( 'azindex_admin_stylesheet', plugins_url('bemoazindex_admin.css', __FILE__) );
   wp_enqueue_style('azindex_admin_stylesheet');
   
}

add_action( 'admin_init', 'azindex_admin_scripts_and_styles' );
?>
