<?php
/*
	To generate A to Z listing  
*/
class AlphabetPlugin {
	//to hold search result from database
	var $result;
	//to hold generated html code
	var $html;
	//to hold link parameter text like /?cat=
	var $link_text;
	//to hold header 
	var $header_text;
	//background color
	var $bg_color; //future use
	//text color
	var $text_color; //future use
	
	//Constructor
	function AlphabetPlugin() 
	{ 
		$this->html = "";
	}

	// load language files
	public function aplhabet_listing_set_lang_file() {
		# set the language file
		$currentLocale = get_locale();
		if(!empty($currentLocale)) {
			$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
			if (@file_exists($moFile) && is_readable($moFile)) {
				load_textdomain(AL_I18N_DOMAIN, $moFile);
			}

		}
	}
	
	//register settings
	public function aplhabet_listing_register_settings() {
		register_setting( 'alphabet-listing-settings-group', 'alphabet-listing-settings', array( &$this, 'validate_settings') );
	}
	
	//validate user input
	public function validate_settings( $settings ) {
		$settings['type'] = (preg_match('/^(post|page|category)$/i', $settings['type']) ? strtolower($settings['type']) : "post");
		$settings['bg_colour'] = (preg_match('/^#[a-f0-9]{6}$/i', $settings['bg_colour']) ? $settings['bg_colour'] : "#f0f0f0");
		$settings['text_colour'] = (preg_match('/^#[a-f0-9]{6}$/i', $settings['text_colour']) ? $settings['text_colour'] : "#424242");
		return $settings;
	}	
	
	// activating the default values
	public function aplhabet_listing_activate() {
		$new_options = array(
			'title' => 'A to Z listing',
			'type' => 'post', 
			'bg_colour' => '#f0f0f0',
			'text_colour' => '#424242'
		);
		add_option('alphabet-listing-settings',$new_options);
	}

	public function alphabet_listing_create_menu() {

		// create new top-level menu
		add_menu_page( 
			__('Alphabet Listing', AL_I18N_DOMAIN),
			__('Alphabet Listing', AL_I18N_DOMAIN),
			"add_users",
			AL_DIRECTORY.'/alphabet_listing_settings.php',
			'',
			plugins_url('/images/icon.png', __FILE__)
		);
		
		
		add_submenu_page( 
			AL_DIRECTORY.'/alphabet_listing_settings.php',
			__("Alphabet Listing Settings", AL_I18N_DOMAIN),
			__("Settings", AL_I18N_DOMAIN),
			"add_users",
			AL_DIRECTORY.'/alphabet_listing_settings.php'
		);
		
		add_submenu_page( 
			AL_DIRECTORY.'/alphabet_listing_settings.php',
			__("Alphabet Listing Help", AL_I18N_DOMAIN),
			__("Help", AL_I18N_DOMAIN),
			"add_users",
			AL_DIRECTORY.'/alphabet_listing_help.php'
		);
	}	


	// deactivating
	public function aplhabet_listing_deactivate() {
		// needed for proper deletion of every option
		delete_option('alphabet-listing-settings');

	}
	
	//To inject the css that is need for rendering alphabets
	public function inject_css()
	{
		wp_register_style( 'prefix-style', AL_URL . "css/alphabet_listing.css" );
    	wp_enqueue_style( 'prefix-style' );
	}

	//To read post table and return all posts
	public function get_all_titles($type, $atts)
	{
		global $wpdb;
		//reset
		$sql = "";
		$this->html = "";
		switch ($type) 
		{
	        case 'post':
                if($atts['category'])
                {
                    $category_name = $atts['category'];
                    $result = get_term_by('name', $category_name, 'category', ARRAY_A);
                    $cat_id = $result['term_id'];
					$args = array(
						'category'        => $cat_id,
						'orderby'         => 'post_date',
						'order'           => 'DESC',
						'post_type'       => 'post',
						'post_status'     => 'publish',
						'suppress_filters' => true 
					);
					$posts_array = get_posts( $args );
					$post_list = array();
					foreach($posts_array as $post){
						$post_data = array('id' => $post->ID, 'post_title'=>$post->post_title);
						array_push($post_list,$post_data);
					}
                }
                else
                {
				    $sql = "select id, post_title from $wpdb->posts where post_status = 'publish' AND post_type = 'post' ORDER BY post_title";
                }
                
	            break;
	        case 'page':
	            $sql = "select id, post_title from $wpdb->posts where post_status = 'publish' AND post_type = 'page' ORDER BY post_title";
	            break;
	        case 'category':
	            $sql = "SELECT term_id as id, name as post_title FROM $wpdb->terms ORDER BY name";
	            break;
	    }
		
	    if($atts['category'])
	    {
	    	$this->result = $post_list;
	    }
	    else{
			$this->result = $wpdb->get_results($sql, ARRAY_A );
	    }	
	}
	/* 
		To generate A to Z html with links 
		note: css need to the there to show the links properly
	*/
	public function generateAtoZHtml()
	{
		
		$startCapital = 65;
		$startSmall = 97;

		$this->html .= "<div id='wp-alphabet-listing'>";
		$this->html .= "<section style=\"background-color:". $this->bg_color .";\">";
		$this->html .= "<h2>". $this->header_text ."</h2>";
		$this->html .= "<ol>\n";
		
		for($i = 0;$i<26;$i++)
		{
			$hasItem = FALSE;
			$tempHtml = "";
			$this->html .= "<li><a style=\"color:". $this->text_color.";\" href='#'>" . chr($startCapital + $i) . "</a>\n";
			foreach($this->result as $row)
			{
				if (( $row['post_title'][0] == chr($startCapital + $i)) || ( $row['post_title'][0] == chr($startSmall + $i)))
				{
					$tempHtml .= "<li><a href='?". $this->link_text ."=". $row['id'] ."'>" .  substr($row['post_title'],0,20) . "</a></li>\n";
					$hasItem = TRUE;
				}
			}
			if ($hasItem)
			{
				$this->html .= "<div>\n" . "<ul>\n" . $tempHtml . "</ul>\n" . "</div>\n";
			}
			
			$this->html .= "</li>\n";
		}
		$this->html .= "</ol>\n";
		$this->html .= "</section>";
		$this->html .= "</div>";
		$this->html .= '<div class="al_clear"></div>';	
	}
	
	//to use shortcode and return html accordingly
	//also sets default values, for default options priority is short code then user settings
	public function atoz_shortcode($atts) 
	{
		$wp_al_options = get_option('alphabet-listing-settings');
		extract(shortcode_atts(array(
						'type' => '',
						'title' => '',
						'bg_color' => '',
						'text_color' => '',
                        'category' => ''
					), $atts));
		//set type
		if ($type == '')
			$type = $wp_al_options['type'];
		//set title
		if ($title == '')
			$this->header_text = $wp_al_options['title'];
		else
			$this->header_text = $title;
		
		//set background color
		if ($bg_color == '')
			$this->bg_color = $wp_al_options['bg_colour'];
		else
			$this->bg_color = $bg_color;
		
		//set text color
		if ($text_color == '')
			$this->text_color = $wp_al_options['text_colour'];
		else
			$this->text_color = $text_color;
							     
	    // check what type user entered
	    switch (strtolower($type)) 
		{
	        case 'post':
				$this->get_all_titles('post', $atts);
				$this->link_text = "p";
	            break;
	        case 'page':
	            $this->get_all_titles('page', $atts);
	            $this->link_text = "page_id";
				break;
	        case 'category':
	            $this->get_all_titles('category', $atts);
				$this->link_text = "cat";
	            break;
			default:
				$this->get_all_titles('post', $atts);
				$this->link_text = "p";
	    }
		$this->generateAtoZHtml();
		return $this->html;
	}
}
