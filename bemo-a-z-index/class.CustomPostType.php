<?php
require_once ('class.CustomPostHelper.php');

class CustomPostType extends CustomPostHelper{

	function __construct()
	{
		$this->custom_post_name = 'azindexcustom';	
	}
}
?>