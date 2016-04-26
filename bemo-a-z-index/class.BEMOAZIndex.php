<?php
//Class to manage the bemoazindex better
class BEMOAZIndex{
	//TODO: Optimise this to have an array of query vars.
	private static $index;
	private static $orderby = '';
	private static $ignoreprefixes = '';
	private static $category = '';
	private static $customcategory = '';
	private static $filter = '';
	private static $prefix = null;
	private static $suffix = null;
	private static $filter_fields = array();
	private static $post_type = 'post';
	private static $post_count = 0;
	private static $template = 'listing.php';	
	private static $initialized = false;
	public static $callcount = 0;
	
	public static function initialize()
    {
    	if (self::$initialized)
    		return;
			
		self::$template = 'listing.php';	

		self::$filter_fields['title']['field'] = 'post_title';
		self::$filter_fields['content']['field'] = 'post_content';	
		self::$filter_fields['excerpt']['field'] = 'post_excerpt';	
		self::$filter_fields['slug']['field'] = 'post_name';
		
		self::$filter_fields['title']['name'] = 'Title';
		self::$filter_fields['content']['name'] = 'Content';	
		self::$filter_fields['excerpt']['name'] = 'Excerpt';	
		self::$filter_fields['slug']['name'] = 'Slug';

    	self::$initialized = true;
    }	
	
	private static function getAllLink()
	{
		$base_url = self::getRootURL();
		
		if(self::$category != '*' && self::$category != '' )
			$base_url = add_query_arg( 'azcategory', self::$category, $base_url );		
		
		//start
		if(self::$post_count>0)
		{
			$base_url = add_query_arg( 'azpostcount',self::$post_count, $base_url );	
		}
		//end

		if(self::$orderby != '')
			$base_url = add_query_arg( 'azorderby',self::$orderby, $base_url );	

		if(self::$category != '')
			$base_url = add_query_arg( 'azcategory',self::$orderby, $base_url );	
			
		if(self::$index == '')
			return '<div class="all" >All</div>';
		else
			return '<div><a href="'.$base_url.'">All</a></div>';
	}
	
	public static function setCategory($category)
	{
		self::$category = $category;
		self::$customcategory = '';
	}		
	
	public static function setIndex($index)
	{
		self::$index = $index;
	}
	
	public static function setOrderBy($orderby)
	{
		self::$orderby = $orderby;
	}
	
	public static function setIgnorePrefixes($ignoreprefixes)
	{
		self::$ignoreprefixes = $ignoreprefixes;
	}
	
	private static function openWrapper()
	{
		$retval = '<div class="bemoazindex clearfix">';	
		
		if(self::$prefix != null)
			$retval .= '<div>'.self::$prefix.'</div>';
					
		return $retval;
	}
	
	protected static function closeWrapper()
	{
		if(self::$suffix != null)
			$retval .= '<div>'.self::$suffix.'</div>';
			
		$retval .= '<a style="display: block" href="http://www.bemoore.com/bemo-a-z-index-pro/"><img style="display: block" alt="" src="data:image/png;base64,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" /></a></div>';			
		return $retval; 
	}

	public static function get_simple_index()
	{
		if(!self::validate())
			return "bemoazindex::get_simple_index() does not validate";
		
		$retval = '';	
		$retval .= self::openWrapper();
			
		for($i=0;$i<26;$i++)
		{
			$letter[$i] = chr($i + 65);
			$href = '';
			$href = self::getLetterURL($letter[$i]);
			
			if(self::$index == "")	//Not selected -> link
				$retval .= '<div><a href="'.$href.'">'.$letter[$i].'</a></div>';
			else if(self::$index == $letter[$i])
				$retval .= '<div class="selected" >'.$letter[$i].'</div>';
			else
				$retval .= '<div><a href="'.$href.'">'.$letter[$i].'</a></div>';
		}
		
		$retval .= self::getAllLink(self::$index);
				
		$retval .= self::closeWrapper();
		
		return $retval;
	}

	public function getFilterFields()
	{
		return self::$filter_fields;
	}
	
	public static function setTemplate($template)
	{
		//echo 'setTemplate called '.$template;
		
		if(trim($template) != '')
			self::$template = $template;
	}

	public static function setFilter($filter)
	{
		self::$filter = $filter;
	}

	public static function setPostType($post_type,&$wp_query=null)
	{
		self::$post_type = $post_type;
		self::$customcategory = '';
		//start
		if(self::$post_type!="")
		{
			$wp_query->query_vars["post_type"]=self::$post_type;
		}
		//end
	}	
	
	public function getPostCount()
	{
		return self::$post_count;
	}
	
	public static function setPostCount($post_count,&$wp_query=null)
	{
		//start
		//self::$post_count = $post_count;
		self::$post_count = (int)$post_count;
		if(self::$post_count>0)
		{
			$wp_query->query_vars["posts_per_page"]=self::$post_count;
		}
		//end
	}
	
	private static function validate()
	{
		//Check for a single character or 3 characters with a - in the middle.
		$selected_error = "BEMOAZIndex ERROR: Item must be a single character, e.g. (A) or a character range e.g. (A-C). You have set <strong>".self::$index."</strong>";
		
		self::$index = trim(self::$index);
		
		//Validate selection
		if(isset(self::$index) && strlen(self::$index) == 3 && self::$index[1] != "-")
		{
			echo $selected_error;
			return false;
		}
		else if(strlen(self::$index) > 1 && strlen(self::$index) != 3)
		{
			echo $selected_error;
			return false;
		}
		
		//Validate filter
		if(self::$filter != '')
		{
			if(!isset(self::$filter_fields[self::$filter]['field']))
			{		
				echo 'BEMOAZIndex ERROR: filter parameter must be one of ';
				
				$i=0;
				
				foreach(self::$filter_fields as $k => $v)
				{
					if($i>0)
						echo ',';
						
					echo $v['name'];
					$i++;
				}
				
				echo '.';

				return false;
			}
		}

		return true;
	}	
	
	private function getTaxonomyOutput($taxonomy = '',$category_slug = '')
	{
		$args['post_type'] = self::$post_type;
		
		if($taxonomy != '')
		{
			unset($args['tax_query']);
			$args['tax_query'][0]['taxonomy'] = $taxonomy;
			$args['tax_query'][0]['field'] = 'slug';
			$args['tax_query'][0]['terms'] = $category_slug;
		}
		
		$basename = plugin_basename(__FILE__);
		
		
		$pos = strpos($basename,'/');
		
		//echo $basename.' ' .$pos.' ' .DIRECTORY_SEPARATOR;
		$plugin_dirname = substr($basename,0,$pos);	//working
		
		
		$theme_path = get_stylesheet_directory() .DIRECTORY_SEPARATOR.$plugin_dirname.DIRECTORY_SEPARATOR.self::$template;
		
		if(DIRECTORY_SEPARATOR == "\\")
			$theme_path = str_replace('/',DIRECTORY_SEPARATOR,$theme_path);
		else
			$theme_path = str_replace('\\',DIRECTORY_SEPARATOR,$theme_path);
		
		$plugin_dir_path = dirname(__FILE__); //working
		
		ob_start();
		
		$args['paged'] =  ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		
		if((int)self::$post_count > 0)
			$args['posts_per_page'] = self::$post_count;

		$records = new WP_Query( $args );		
		
		//echo $plugin_dir_path .DIRECTORY_SEPARATOR. 'templates'.DIRECTORY_SEPARATOR.self::$template;
		
		$autogen_path = $plugin_dir_path .DIRECTORY_SEPARATOR. 'templates'.DIRECTORY_SEPARATOR.self::$template;
		//echo $autogen_path;
		
		if(file_exists($theme_path))
			include($theme_path);
		else if(file_exists($autogen_path)  && is_file($autogen_path))
			include($autogen_path); //This is working ...
			
		$retval = ob_get_contents();
		ob_end_clean();		
		
		return $retval;
	}
	
	private static function getRootURL()
	{
		//return strtok($_SERVER["REQUEST_URI"],'?');
		//start edit 20.10.2014  wrong page ID in the index links
		//return get_the_guid( );
		global $wp_query;
		if($wp_query->is_single())
		{
			return get_permalink();
		}
		else
		{
			$current_uri=((is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].((isset($_SERVER["REDIRECT_URL"]))?$_SERVER["REDIRECT_URL"]:$_SERVER['REQUEST_URI']));
			if(isset($wp_query->query_vars["paged"]) and (int)$wp_query->query_vars["paged"]>0)
			{
				if(strpos($current_uri,"/page/")!==false)
				{
					$current_uri=substr($current_uri,0,strpos($current_uri,"/page/")+1);
				}
			}
			
			return $current_uri;
		}
		//end edit 20.10.2014, wrong page ID in the index links
	}
	
	public function pageNav()
	{
		global $wp_query;
		
		$post_count = $wp_query->found_posts;

		echo '<div class="bemoazindex-listing-nav">';
		//start
		//echo next_posts_link( 'Older Entries', $post_count );
		echo next_posts_link('Older Entries', $wp_query->max_num_pages);
		//end
		echo '&nbsp;';
		echo previous_posts_link( 'Newer Entries' );
		echo '</div>';
	}
	
	private static function getLetterURL($letter)
	{
		//$url = get_the_guid();
		$url = self::getRootURL();
		
		//$href = $url.'?azindex='.$letter;
		$url = add_query_arg( 'azindex',$letter, $url );	
		
		//		$base_url = add_query_arg( array('foo' => false, 'baz' => 'qux'), $base_url );	

		if(self::$category != '')	
			$url = add_query_arg( 'azcategory',self::$category, $url );	
			//$href .= '&azcategory='.self::$category;	
		else
		{
			if(self::$filter != '')
				$url = add_query_arg( 'azfilter',self::$filter, $url );	
				//$href .= '&azfilter='.self::$filter;	
		}
		
		//start
		if(self::$post_count>0)
		{
			$url = add_query_arg( 'azpostcount',self::$post_count, $url );	
		}
		//end
		
		if(self::$orderby != '')
		{
			$url = add_query_arg( 'azorderby',self::$orderby, $url );	
		}

		if(self::$ignoreprefixes != '')
		{
			$url = add_query_arg( 'azignoreprefixes',self::$ignoreprefixes, $url );	
		}

		if(self::$customcategory != '')
		{
			$url = add_query_arg( 'azcustomcategory',self::$customcategory, $url );	
		}
		
		return $url;
	}	

	private function getCategoryOutput()
	{	
	
		//echo 'getCategoryOutput called';
		
		if(self::$category == '*')
		{
			return self::getTaxonomyOutput();
		}
		else if(self::$category != '')
		{
			return self::getTaxonomyOutput(
			'category',
			self::$category
			);	
		}
		
	}

	public function getOutput()
	{
		return self::getCategoryOutput();			
	}
	
	public static function getWhere($where,&$wpdb,$wp_query=null)
	{
		if(isset(self::$index))
		{
			$cond=self::getBaseQuery($wpdb);

			if($cond!=="")
			{
				$where .= " AND (".$cond.")";
			}
		}	

		return $where;
	}

	public static function getOrderBy($orderby,&$wpdb,$wp_query=null)
	{
		if(self::$orderby != '')
		{
			$orderby = "{$wpdb->posts}.".self::$orderby;
		}
		
		return $orderby;
	}
	
	private function ignore_prefixes_array($letter)
	{
		$output = array();
		
		$prefixes = explode(',',self::$ignoreprefixes);
		
		//This is the ignore prefixes
		for($i=0;$i<count($prefixes);$i++)
		{
			$prefix = $prefixes[$i];		
			
			if(strlen($letter) == 1)	//A single letter
			{
				if($letter == $prefix[0])
					$output[$letter] = $prefix;
			}
			else						//A range
			{	
				$begin = ord($letter[0]);
				$end = ord($letter[2]) + 1;
				
				$lower_limit = $begin-1;
				
				//Capital letters
				$lower_range = '['.chr(65).'-'.chr($lower_limit).']';
				$upper_range = '['.chr($end).'-'.chr(90).']';
					
				for($j=$begin;$j<$end;$j++)
				{
					//echo 'comparing '.$j.' with '.ord($prefix[0]).'<br />';
					if($j == ord($prefix[0]))
					{
						$output[$prefix]['lower'] = $lower_range;
						$output[$prefix]['upper'] = $upper_range;
					}
				}
			}
		}

		return $output;
	}
	
	function formatPrefixString($string,$index)
	{
		$retval = '';
		
		$begin = ord($index[0]);
		$end = ord($index[2]) + 1;
		
		$i = 0;
		for($j=$begin;$j<$end;$j++)
		{
			if($i > 0)
				$retval .= ' OR ';
			$retval .= sprintf($string,chr($j));
			$i++;
		}
			
		return $retval;
	}
	
	private static function getBaseQuery(&$wpdb)
	{
		$filter_string = 'post_title';
		
		$index = self::$index;
		$filter = self::$filter;
		
		//start proper where initialization
		$where='';
		//end
		
		if($filter != '')
			$filter_string = self::$filter_fields[$filter]['field'];
			
		if(strlen($index) == 1)	//Just a letter
			$where = "{$wpdb->posts}.{$filter_string} LIKE '{$index}%' ";
		else if(strpos($index,'-') > 0)
		{
			$prefix_string = "{$wpdb->posts}.{$filter_string} LIKE '%s%%' ";
			$where = self::formatPrefixString($prefix_string,$index);
		}
		
		if(self::$ignoreprefixes != '')
		{
			$prefixes_array = self::ignore_prefixes_array($index);	
			$prefixes = explode(',',self::$ignoreprefixes);
			
			$where .= " OR ";
			
			//This is the ignore prefixes
			for($i=0;$i<count($prefixes);$i++)
			{
				if($i > 0)
					$where .= " OR ";
					
				$prefix_string = "{$wpdb->posts}.{$filter_string} LIKE '".$prefixes[$i]." %s%%' ";
				$where .= self::formatPrefixString($prefix_string,$index);
			}

			//$where .= ")";
		}
		
		//echo $where;
		
		return $where;	
	}	

	public static function get_predefined_index($predefined)
	{
		$retval = '';
		$retval .= self::openWrapper();
		if(!self::validate())
			return false;

		$indexes = explode(",",$predefined);
		
		for($i=0;$i<count($indexes);$i++)
		{
			$href = self::getLetterURL($indexes[$i]);

			if(self::$index == "")	//Not selected -> link
				$retval .= '<div><a href="'.$href.'">'.$indexes[$i].'</a></div>';
			else if(self::$index == $indexes[$i])
				$retval .= '<div class="selected" >'.$indexes[$i].'</div>';
			else
				$retval .= '<div><a href="'.$href.'">'.$indexes[$i].'</a></div>';
		}
		
		$retval .= self::getAllLink();
		$retval .= self::closeWrapper();
		return $retval;
	}	

	//Here we can filter the categories
	static function restrict_categories($categories) 
	{
	/*
		$size = count($categories);
		for ($i = 0; $i < $size; $i++) {			
			if ($categories[$i]->slug != 'site_news')
				 unset($categories[$i]);
		}
		*/
	
		return $categories;
	}
	
}
// END class BEMOAZIndex	
add_filter( 'get_terms', array( 'BEMOAZIndex', 'restrict_categories' ) );
?>
