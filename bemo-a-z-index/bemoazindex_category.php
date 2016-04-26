<?php
/* Add to category */
//add extra fields to category edit form hook
add_action ( 'edit_category_form_fields', 'azindex_extra_category_fields');
//add extra fields to category edit form callback function
function azindex_extra_category_fields( $tag ) 
{    //check for existing featured ID
    $t_id = $tag->term_id;
    $cat_meta = get_option( "category_$t_id");
    //print_r($tag);
?>
<tr>
	<td colspan="2">
		<h2>A-Z Index Settings</h2>
		<p>NOTE: For this to work, in your theme (<?php echo get_stylesheet_directory();?>) you need to put the following code in the file</p>
		<p>category-<?php echo $tag->slug;?>.php if it exists or</p>
		<p>category.php if category-<?php echo $tag->slug;?>.php doesn't exist:</p>
		<?php echo htmlspecialchars('<?php azindex_category(); ?>'); ?>
	</td>
</tr>
<tr class="form-field">
<th scope="row" valign="top">
	<label for="azindex"><?php _e('Use A-Z Index?'); ?></label></th>
<td>
	<?php $checked = isset($cat_meta['azindex']) && $cat_meta['azindex'] != '' ? 'checked' : ''; ?> 
<input type="checkbox" name="Cat_meta[azindex]" id="Cat_meta[azindex]" size="3" style="float: left" value="1" <?php echo $checked; ?> ><br />
            <span class="description"><?php _e('Use A-Z Index with this category?'); ?></span>
        </td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="index"><?php _e('Index Value'); ?></label></th>
<td>
<input type="text" name="Cat_meta[index]" id="Cat_meta[index]" size="25" style="width:60%;" value="<?php echo isset($cat_meta['index']) ? $cat_meta['index'] : ''; ?>"><br />
            <span class="description"><?php _e('Index value : e.g. A-E,F-J,K-M,N-R,S-W,X-Z,0,1,2,3,4,5,6,7,8,9 or blank for default'); ?></span>
        </td>
</tr>
<!--
<tr class="form-field">
<th scope="row" valign="top"><label for="ignoreprefixes"><?php _e('Ignore Prefixes'); ?></label></th>
<td>
<input type="text" name="Cat_meta[ignoreprefixes]" id="Cat_meta[ignoreprefixes]" size="25" style="width:60%;" value="<?php echo isset($cat_meta['ignoreprefixes']) ? $cat_meta['ignoreprefixes'] : ''; ?>"><br />
            <span class="description"><?php _e('Prefixes to ignore : e.g. (A,The would also list The Little House on the Prairie under L) or blank for default'); ?></span>
        </td>
</tr>
-->
<tr class="form-field">
<th scope="row" valign="top">
	<label for="target"><?php _e('Target'); ?></label></th>
	<td>
<?php
	$target = isset($cat_meta['target']) ? $cat_meta['target']:'';
	$targets = array();
	$targets[0]['name'] = "Default";
	$targets[0]['value'] = '';
	$targets[0]['selected'] = '';
	
	for($i=1;$i<20;$i++)
	{
		$targets[$i]['name'] = $i;
		$targets[$i]['value'] = $i;
		$targets[$i]['selected'] = '';
		
		if((int)$target == (int)$i)
			$targets[$i]['selected'] = 'selected';
	}
?>
	<select name="Cat_meta[target]" >
<?php
	for($i=0;$i<count($targets);$i++)
		echo '<option value="'.$targets[$i]['value'].'" '.$targets[$i]['selected'].' >'.$targets[$i]['name'].'</option>';
?>	
	</select>
	
		
	</td>
</tr>

<tr class="form-field">
<th scope="row" valign="top">
	<label for="debug"><?php _e('Debug Mode?'); ?></label></th>
<td>
	<?php $checked = isset($cat_meta['debug']) && $cat_meta['debug'] != ''  ? 'checked' : ''; ?> 
<input type="checkbox" name="Cat_meta[debug]" id="Cat_meta[debug]" size="3" style="float: left" value="1" <?php echo $checked; ?> ><br />
            <span class="description"><?php _e('Turn on debugging?'); ?></span>
        </td>
</tr>
<?php
}

// save extra category extra fields hook
add_action ( 'edited_category', 'azindex_save_extra_category_fields');
   // save extra category extra fields callback function
function azindex_save_extra_category_fields( $term_id ) 
{
    if ( isset( $_POST['Cat_meta'] ) ) 
    {
        $t_id = $term_id;
        $cat_meta = get_option( "category_$t_id");
        $cat_keys = array_keys($_POST['Cat_meta']);

		foreach ($cat_keys as $key)
		{
			if (isset($_POST['Cat_meta'][$key]))
				$cat_meta[$key] = $_POST['Cat_meta'][$key];
		}
        
        //Now catch ones that may be unset
		if (!isset($_POST['Cat_meta']['debug']))
			$cat_meta['debug'] = '';

		if (!isset($_POST['Cat_meta']['azindex']))
			$cat_meta['azindex'] = '';
        
        //save the option array
        update_option( "category_$t_id", $cat_meta );
    }
}

function azindex_category()
{
	if(is_admin())
		return;
			
	global $wp_query;
	
    if(is_object($wp_query->tax_query))
    {
		if(isset($wp_query->tax_query->queries[0]['taxonomy']))
		{
			if($wp_query->tax_query->queries[0]['taxonomy'] == 'category')
			{
				$cat_obj =  get_category_by_slug( $wp_query->tax_query->queries[0]['terms'][0] );
				$t_id = $cat_obj->term_id;
				
				$cat_meta = get_option( "category_$t_id");
				
				if(isset($cat_meta))
				{
					if(isset($cat_meta['azindex']) && (int)$cat_meta['azindex'] == 1)
					{
						$atts = array();
						
						if(isset($cat_meta['debug']) && (int)$cat_meta['debug'] == 1)
							$atts['debug'] = 'true';

						if(isset($cat_meta['index']) && $cat_meta['index'] != '')
							$atts['index'] = $cat_meta['index'];
							
						if(isset($cat_meta['filter']) && $cat_meta['filter'] != '' )
							$atts['filter'] = $cat_meta['filter'];
/*
						if(isset($cat_meta['ignoreprefixes']) && $cat_meta['ignoreprefixes'] != '' )
							$atts['ignoreprefixes'] = $cat_meta['ignoreprefixes'];
*/
						echo azindex_get_index($atts);
					}
				}	
			}
		}
	}
	
}

add_filter('manage_edit-category_columns', 'azindex_category_table_header', 10, 2);

function azindex_category_table_header($cat_columns)
{
    $cat_columns['cat_azindex'] = 'A-Z Index';
    return $cat_columns;
}

function azindex_category_custom_fields($deprecated,$column_name,$term_id)
{
	if ($column_name == 'cat_azindex') 
	{
		$cat_meta = get_option( "category_$term_id");	 
		
		if(isset($cat_meta['azindex']) && (int)$cat_meta['azindex'] == 1)
			echo 'Y';
	}
}
add_filter ('manage_category_custom_column', 'azindex_category_custom_fields', 10,3);
?>
