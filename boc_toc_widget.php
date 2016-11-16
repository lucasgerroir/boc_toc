<?php



/**
 * Adds Boc_Toc_Widget widget.
 */
class Boc_Toc_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'Boc_Toc_Widget', // Base ID
			__('Boc Toc Widget', 'boc_toc_domain'), // Name
			array( 'description' => __( 'A dynamic table of contents', 'boc_toc_domain' ), ) // Args
		);

		// add the action to enqueue js and css scripts	
        add_action('wp_enqueue_scripts', array(&$this, 'js'));
        add_action('wp_enqueue_scripts', array(&$this, 'css'));

        //default headers to look for
        $this->header_array = ["h1", "h2", "h3", "h4", "h5", "h6"];
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		$headers_list = "";
		$filtered_instance = array_filter($instance["header_select"]);
		
		// compile the selected headers for the data attribute
		foreach ( $filtered_instance as $header_name => $check ) {			

			$headers_list .= $header_name;

			if ($header_name != end(array_keys($filtered_instance))) {
				$headers_list .= ", ";
			} 
		}
		
		// print the selected font and active colors in a inline style tag
		echo "<style>";
		echo "#boc_toc_container.spyscroll .nav > li > a { color: " . $instance["font_color"] . ";}";
		echo "#boc_toc_container.spyscroll .nav > .active > a { color: " . $instance["active_color"] . "; border-left: 2px solid "  . $instance["active_color"] . "; }";
		echo "</style>";

		//print the main container div and add the data attributes to it
		echo "<div id='boc_toc_container' data-depth='". $instance["depth"] ."' data-spyscroll='". $instance["spyscroll"]  ."' data-headers='" . $headers_list . "'>";
		echo $args['before_widget'];
		
		//print the chosen title
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		echo $args['after_widget'];
		echo "</div>";
	}
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		// check if inputs set or set to default
		( isset( $instance['content_div'] )  ? $content_div = $instance[ 'content_div' ]  : $content_div = ".entry-content");
		( isset( $instance['title'] )  ? $title = $instance[ 'title' ]  : $title = __( 'New title', 'text_domain' ));
		( isset( $instance['active_color'] )  ? $active_color = $instance[ 'active_color' ]  : "#FFFFFF");
		( isset( $instance['font_color'] )  ? $active_color = $instance[ 'font_color' ]  : "#FFFFFF");
		( ! empty( $instance['spyscroll'] )  ? $spyscroll = "checked"  : $spyscroll = "");
		
		$headers = []; 
		$depths = [];

		foreach ($this->header_array as $header) {
			 ( ! empty( $instance['header_select'][ $header] )  ? $headers[$header] = "checked"  : $headers[$header] = "");
			 (( $instance['depth'] ==  $header)  ? $depths[$header] = "selected"  : $depths[$header] = "");
		}
		
		// the admin UI
		?>
		
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">

		<hr />
		<p>Choose the headers to include</p>
			
		<?php
		
		// Add the input for the header selection 
		foreach ($this->header_array as $header) {
		 	
		 	$element_id = $this->get_field_id( 'header_select_' . $header ); 
		 	$field_name = $this->get_field_name( 'header_select_' . $header );

		?>		
			<label for="<?php echo $element_id ?>"> <?php echo $header; ?></label>
			<input type="checkbox" <?php echo $headers[$header]; ?> id="<?php echo $element_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo 'header_select_' . $header; ?>">
		
<?php } ?>
			<br />
			<hr />
			<p>Turn spyscroll on or off. If spyscroll is off its position is relative to its parent</p>
			<label for="<?php echo $element_id ?>"> Add Spyscroll </label>
			<input type="checkbox" <?php echo $spyscroll ?> id="<?php echo $this->get_field_id( 'spyscroll' ); ?>" name="<?php echo $this->get_field_name( 'spyscroll' ); ?>" value="spyscroll">
			<br />
			<hr />

			<p>Select the depth of table of contents</p>
			<select id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>">
			
			<?php
			// Add the input for the header selection 
		 	foreach ($this->header_array as $header) { ?>

				<option value="<?php echo $header; ?>" <?php echo $depths[$header]; ?> > <?php echo $header; ?> </option>

	  <?php } ?>
			
			</select>
			
			<hr />

			<label for="<?php echo $this->get_field_id( 'content_div' ); ?>">Content Selector:</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'content_div' ); ?>" name="<?php echo $this->get_field_name( 'content_div' ); ?>" type="text" value="<?php echo esc_attr( $content_div ); ?>">
			
			<hr />
			
			<label for="<?php echo $this->get_field_id( 'active_color' ); ?>">Active Color:</label> 
			<input class="my-color-picker" type="text" id="<?php echo $this->get_field_id( 'active_color' ); ?>" name="<?php echo $this->get_field_name( 'active_color' ); ?>" value="<?php echo esc_attr( $instance['active_color'] ); ?>" /> 

			<hr>

			<label for="<?php echo $this->get_field_id( 'font_color' ); ?>">Font Color:</label> 
			<input class="my-color-picker" type="text" id="<?php echo $this->get_field_id( 'font_color' ); ?>" name="<?php echo $this->get_field_name( 'font_color' ); ?>" value="<?php echo esc_attr( $instance['font_color'] ); ?>" /> 

			<br />
			<br />

		<?php 
	}
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['content_div'] = ( ! empty( $new_instance['content_div'] ) ) ? strip_tags( $new_instance['content_div'] ) : '';
		$instance['spyscroll'] = ( ! empty( $new_instance['spyscroll'] ) ) ? 'checked'  : '';
		$instance['depth'] = ( isset( $new_instance['depth'] ) ) ? trim($new_instance['depth'])  : '';
		$instance['active_color'] = $new_instance['active_color'];
		$instance['font_color'] = $new_instance['font_color'];

		foreach ($this->header_array as $header) {
			$instance['header_select'][ $header ] = ( ! empty( $new_instance['header_select_' . $header] ) ) ? 'checked'  : '';
		}

		return $instance;
	}

	/**
	 * enqueue the required Javascript files only if on page with active widget
	 */
	public function js( ) {
		
        if ( is_active_widget(false, false, $this->id_base, true) ) {
           wp_enqueue_script('jquery');
           wp_enqueue_script('scrolllspy', plugins_url('/js/scrollspy.js', __FILE__));
           wp_enqueue_script('my-script', plugins_url('/js/Boc_Toc.js', __FILE__));  
        }

	}

	/**
	 * enqueue the required css files only if on page with active widget
	 */
	public function css( ) {
		
		if ( is_active_widget(false, false, $this->id_base, true) ) {
			wp_enqueue_style('my-style', plugins_url('/css/boc_toc.css', __FILE__));   
		}        
	}
} // class Boc_Toc_Widget