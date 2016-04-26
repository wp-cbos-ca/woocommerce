<?php
// Creating the widget 
class wp_bemoazindex_output_widget extends WP_Widget {

protected $fieldnames = array();

function __construct() {
	$this->fieldnames['template']['name'] = 'Template';
	$this->fieldnames['template']['default'] = 'listing.php';
	
	parent::__construct(
	// Base ID of your widget
	'wp_bemoazindex_output_widget', 

	// Widget name will appear in UI
	__('WP BEMO A-Z Index Output Widget', 'wp_bemoazindex_output_widget_domain'), 

	// Widget description
	array( 'description' => __( 'Adds an A-Z Index Output widget to your widget area', 'wp_bemoazindex_output_widget_domain' ), ) 
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

// This is where you run the code and display the output
//echo __( 'Hello, World!', 'wp_bemoazindex_output_widget_domain' );

	$shortcode = '[azindexoutput';
	
	if($this->fieldnames['template']['value'] != '')
		$shortcode .= ' template="'.$this->fieldnames['template']['value'].'" ';
		
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
			$this->fieldnames[$k]['value'] = __( $this->fieldnames[$k]['default'], 'wp_bemoazindex_output_widget_domain' );
		}
	}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" type="text" value="<?php echo esc_attr( $this->fieldnames['template']['value'] ); ?>" />
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
} // Class wp_bemoazindex_output_widget ends here

// Register and load the widget
function bemoazindex_load_output_widget() {
	register_widget( 'wp_bemoazindex_output_widget' );
}
add_action( 'widgets_init', 'bemoazindex_load_output_widget' );
?>