<?php
// Creating the widget 
class wp_bemoazindex_widget extends WP_Widget {

protected $fieldnames = array();

function __construct() {
	$this->fieldnames['title']['name'] = 'Title';
	$this->fieldnames['title']['default'] = 'New Title';
	$this->fieldnames['index']['name'] = 'Custom Index';
	$this->fieldnames['index']['default'] = '';
	$this->fieldnames['filter']['name'] = 'Filter';
	$this->fieldnames['filter']['default'] = '';
	$this->fieldnames['posttype']['name'] = 'Post Type (azindex for A-Z Index)';
	$this->fieldnames['posttype']['default'] = 'post';
	$this->fieldnames['category']['name'] = 'Post Category (* for all categories)';
	$this->fieldnames['category']['default'] = '';
	$this->fieldnames['postcount']['name'] = 'Posts To Show (0 for no pagination)';
	$this->fieldnames['postcount']['default'] = '';
	
	
	parent::__construct(
	// Base ID of your widget
	'wp_bemoazindex_widget', 

	// Widget name will appear in UI
	__('WP BEMO A-Z Index Widget', 'wp_bemoazindex_widget_domain'), 

	// Widget description
	array( 'description' => __( 'Adds an A-Z Index widget to your widget area', 'wp_bemoazindex_widget_domain' ), ) 
	);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
	foreach($this->fieldnames as $k => $v)
		$this->fieldnames[$k]['value'] = apply_filters( 'widget_title', $instance[$k] );

	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	if ( ! empty( $this->fieldnames['title']['value'] ) )
	echo $args['before_title'] . $this->fieldnames['title']['value'] . $args['after_title'];

	$shortcode = '[azindex';

	if($this->fieldnames['index']['value'] != '')
		$shortcode .= ' index="'.$this->fieldnames['index']['value'].'" ';
		
	if($this->fieldnames['filter']['value'] != '')
		$shortcode .= ' filter="'.$this->fieldnames['filter']['value'].'" ';

	if($this->fieldnames['category']['value'] != '')
		$shortcode .= ' category="'.$this->fieldnames['category']['value'].'" ';

	if($this->fieldnames['posttype']['value'] != '')
		$shortcode .= ' posttype="'.$this->fieldnames['posttype']['value'].'" ';
		
	if($this->fieldnames['postcount']['value'] != '')
		$shortcode .= ' postcount="'.$this->fieldnames['postcount']['value'].'" ';
	

	$shortcode .= ']';

	echo do_shortcode($shortcode);

	echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {

	foreach($this->fieldnames as $k => $v)
	{
		if ( isset( $instance[ $k ] ) ) {
			$this->fieldnames[$k]['value'] = $instance[ $k ];
		}
		else {
			$this->fieldnames[$k]['value'] = __( $this->fieldnames[$k]['default'], 'wp_bemoazindex_widget_domain' );
		}
	}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( $this->fieldnames['title']['name'].':' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $this->fieldnames['title']['value'] ); ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'index' ); ?>"><?php _e( $this->fieldnames['index']['name'].':' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'index' ); ?>" name="<?php echo $this->get_field_name( 'index' ); ?>" type="text" value="<?php echo esc_attr( $this->fieldnames['index']['value'] ); ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( $this->fieldnames['filter']['name'].':' ); ?></label> 
<select class="widefat"  name="<?php echo $this->get_field_name( 'filter' ); ?>" id="<?php echo $this->get_field_id( 'filter' ); ?>" >
<?php
	require_once('class.BEMOAZIndex.php');

	$index = new BEMOAZIndex();
	$fields = $index->getFilterFields();
	
	foreach($fields as $k => $v)
	{
		if($this->fieldnames['filter']['value'] == $k)
			echo '<option value="'.$k.'" selected >'.$v['name'].'</option>';
		else
			echo '<option value="'.$k.'" >'.$v['name'].'</option>';
	}
?>
</select>
</p>
<p>
<label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _e( $this->fieldnames['posttype']['name'].':'); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'posttype' ); ?>" name="<?php echo $this->get_field_name( 'posttype' ); ?>" type="text" value="<?php echo esc_attr( $this->fieldnames['posttype']['value'] ); ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( $this->fieldnames['category']['name'].':'); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>" type="text" value="<?php echo esc_attr( $this->fieldnames['category']['value'] ); ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'postcount' ); ?>"><?php _e( $this->fieldnames['postcount']['name'].':' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'postcount' ); ?>" name="<?php echo $this->get_field_name( 'postcount' ); ?>" type="text" value="<?php echo esc_attr( $this->fieldnames['postcount']['value'] ); ?>" />
</p>

<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();

	foreach($this->fieldnames as $k => $v)
		$instance[$k] = ( ! empty( $new_instance[$k] ) ) ? strip_tags( $new_instance[$k] ) : '';	
	
	return $instance;
}

} // Class wp_bemoazindex_widget ends here

// Register and load the widget
function bemoazindex_load_widget() {
	register_widget( 'wp_bemoazindex_widget' );
}
add_action( 'widgets_init', 'bemoazindex_load_widget' );
?>