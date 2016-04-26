<?php

class CustomPostHelper{
	protected $fieldnames = array();
	protected $custom_post_name = '';
	protected $currency_symbol = '';
	
	function __construct()
	{
	
	}
	
	protected function getColumnID($fieldname)
	{
		return $this->custom_post_name.'_col_ev_'.$fieldname;
	}
	
	protected function getFieldnameFromColumn($column)
	{
		$column_stub = $this->custom_post_name.'_col_ev_';
		
		return str_replace($column_stub,'',$column);
	
	}
	
	public function GenerateForm($id)
	{
		/*global $post_type;
	
		if($post_type != $this->custom_post_name)
			return;		
	*/
		$retval = '';
		//echo 'CustomPostHelper::GenerateForm() called';
		foreach($this->fieldnames as $key => $val)
		{
			$value = esc_html( get_post_meta( $id, $key, true ) );
		

			if($val['type'] == 'date')
			{
				$retval .= '<div>
					<label for="'.$key.'">'.$val['label'].'</label>
					<input type="text" size="30" class="bemopaymentsdate" name="'.$key.'" value="'.$value.'" />
				</div>';			
			}			
			else if($val['type'] == 'time')
			{
				$retval .= '<div>
					<label for="'.$key.'">'.$val['label'].'</label>
					<input type="text" size="30" class="bemopaymentstime" name="'.$key.'" value="'.$value.'" />
				</div>';			
			}
			else if($val['type'] == 'currency')
			{
				$retval .= '<div>
					<label for="'.$key.'">'.$val['label'].'&nbsp;('.$this->currency_symbol.')&nbsp;</label>
					<input type="text" size="30" name="'.$key.'" value="'.$value.'" />
				</div>';						
			}
			else if($val['type'] == 'checkbox')
			{
				$checked = '';
				
				if($value == 1)
					$checked = 'checked';
			
				$retval .= '<div>
					<label for="'.$key.'">'.$val['label'].'</label>
					<input name="'.$key.'" type="checkbox" value="1" '.$checked.'  />
				</div>';						
			}
			else
			{
				$retval .= '<div>
					<label for="'.$key.'">'.$val['label'].'</label>
					<input type="text" size="30" name="'.$key.'" value="'.$value.'" />
				</div>';
			}
		}

		return $retval;				
	}
	
	public function AddMetaBox($title)
	{
		add_meta_box( $this->custom_post_name.'_meta_box',
				$title,
				'display_'.$this->custom_post_name.'_meta_box',
				$this->custom_post_name, 'normal', 'high'
		);
	}
	
	public function GenerateCustomColumns($column,$custom)
	{
	//echo 'CustomPostHelper::GenerateCustomColumns() called';
		/*global $post_type;
	
		if($post_type != $this->custom_post_name)
			return;		
	*/
		$fieldname = $this->getFieldnameFromColumn($column);
			
		if($this->fieldnames[$fieldname]['type'] == 'checkbox')
		{
			if($custom[$fieldname][0] == 0)
				echo 'No';
			else
				echo 'Yes';
		}
		else if($this->fieldnames[$fieldname]['type'] == 'currency')			
			echo $this->currency_symbol.'&nbsp;'.$custom[$fieldname][0];		
		else
			echo $custom[$fieldname][0];
	}
	
	public function AddPaymentFields($id)
	{
	//echo 'CustomPostHelper::AddPaymentFields() called';
		foreach($this->fieldnames as $key => $val)
		{
			if ( isset( $_POST[$key] ) && $_POST[$key] != '' )
				update_post_meta( $id, $key, $_POST[$key] );
		}	
	}
	
	public function GetEditColumns()
	{
	//echo 'CustomPostHelper::GetEditColumns() called';	
		$columns = array();

		$columns["cb"] = "<input type=\"checkbox\" />";
        $columns["title"] = "Payment Item";
 		
		foreach($this->fieldnames as $key => $val)
			$columns[$key] = $val['label'];
	
		return $columns;		
	}
	
	public function AddRecord($post_details)
	{
		// Initialize the page ID to -1. This indicates no action has been taken.
		$post_id = -1;
		// Setup the author, slug, and title for the post
		$title = 'My Example Post';
		// If the page doesn't already exist, then create it

		$post_id = wp_insert_post(
		  array(
//			'comment_status'  => 'closed',
//			'ping_status'   => 'closed',
			'post_author'   => 1,
//			'post_name'   => $slug,
			'post_title'    => $post_details['item'],
			'post_status'   => $post_details['status'],
			'post_type'   => $this->custom_post_name
		  )
		);
		
		//Update the meta data
		foreach($this->fieldnames as $key => $val)
		{
			if ( isset( $post_details[$key] ) && $post_details[$key] != '' )
				update_post_meta( $post_id, $key, $post_details[$key] );
		}	
		
		return $post_id;

	}
}
?>