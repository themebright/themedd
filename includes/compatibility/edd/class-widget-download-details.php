<?php

/**
 * Themedd Download Details Information Widget
 * Inspired by the same widget from the Vendd theme: https://easydigitaldownloads.com/downloads/vendd/
 *
 * This widget is designed to replace the default download info widget that
 * displays in the Themedd download sidebar by default. This purely exists
 * as an alternative to the default so that you can control your sidebar
 * and rearrange items.
 *
 * @since 1.0.0
 */
class Themedd_Download_Details extends WP_Widget {

	/**
	 * Register the widget
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			'themedd_download_details',
			'Themedd' . ': ' . sprintf( __( '%s Details', 'themedd' ), edd_get_label_singular() ),
			array(
			'description' => sprintf( __( 'Display the %s details such as date published and number of sales.', 'themedd' ), strtolower( edd_get_label_singular() ) ),
			'classname'   => 'downloadDetails'
			)
		);

	}

	/**
	 * Output the content of the widget
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		global $post;

		// Return early if not a single download.
		if ( 'download' !== get_post_type( $post ) ) {
			return;
		}

		// Get the title.
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		/**
		 * Download details widget defaults.
		 * The values of the widget settings are passed into themedd_edd_download_details_options()
		 */
		$options = apply_filters( 'themedd_edd_download_details_widget_defaults',
			array(
				'version'        => isset( $instance['version'] ) ? $instance['version'] : false, // Show the version number. This is only set when Software Licensing is active.
				'sale_count'     => $instance['sales'],                                           // Show the sale count.
				'date_published' => $instance['published'],                                       // Show the published date.
				'categories'     => $instance['categories'],                                      // Show the categories.
				'tags'           => $instance['tags'],                                            // Show the tags.
				'title'          => $title
			), $instance
		);

		$options = themedd_edd_download_details_options( $options );

		// Return if download details cannot be shown.
		if ( ! themedd_edd_show_download_details( $options ) ) {
			return;
		}

		echo $args['before_widget'];

		if ( ! empty( $options['title'] ) && $options['title'] ) {
			echo $args['before_title'] . $options['title'] . $args['after_title'];
		}

		?>

		<ul>

			<?php do_action( 'themedd_edd_sidebar_download_details_list_start', $options ); ?>

			<?php
			/**
			 * Published
			 */
			if ( true === $options['date_published'] ) :
			?>
				<li class="downloadDetails-datePublished">
					<span class="downloadDetails-name"><?php _e( 'Published:', 'themedd' ); ?></span>
					<span class="downloadDetails-value"><?php echo themedd_edd_download_date_published(); ?></span>
				</li>
			<?php endif; ?>

			<?php
			/**
			 * Sale count
			 */
			if ( true === $options['sale_count'] ) :
				$sales = edd_get_download_sales_stats( $post->ID );
			?>
				<li class="downloadDetails-sales">
					<span class="downloadDetails-name"><?php _e( 'Sales:', 'themedd' ); ?></span>
					<span class="downloadDetails-value"><?php echo $sales; ?></span>
				</li>
			<?php endif; ?>

			<?php
			/**
			 * Version.
			 */
			if ( true === $options['version'] ) :

				$version = themedd_edd_download_version( $post->ID );

				if ( $version ) : ?>
				<li class="downloadDetails-version">
					<span class="downloadDetails-name"><?php _e( 'Version:', 'themedd' ); ?></span>
					<span class="downloadDetails-value"><?php echo $version; ?></span>
				</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php
			/**
			 * Download categories.
			 */
			if ( true === $options['categories'] ) :

				$categories = themedd_edd_download_categories( $post->ID );

				if ( $categories ) : ?>
					<li class="downloadDetails-categories">
						<span class="downloadDetails-name"><?php _e( 'Categories:', 'themedd' ); ?></span>
						<span class="downloadDetails-value"><?php echo $categories; ?></span>
					</li>
				<?php endif; ?>

		 	<?php endif; ?>

			<?php
			/**
			 * Tags.
			 */
			if ( true === $options['tags'] ) :

				$tags = themedd_edd_download_tags( $post->ID );

				if ( $tags ) : ?>
				<li class="downloadDetails-tags">
					<span class="downloadDetails-name"><?php _e( 'Tags:', 'themedd' ); ?></span>
					<span class="downloadDetails-value"><?php echo $tags; ?></span>
				</li>
				<?php endif; ?>

			<?php endif; ?>

			<?php do_action( 'themedd_edd_sidebar_download_details_list_end', $options ); ?>

		</ul>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		// Default settings.
		$defaults = array(
			'published'  => true,
			'sales'      => true,
			'version'    => true,
			'categories' => true,
			'tags'       => true
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		?>
		<p class="themedd-widget-usage"><em><?php _e( 'Only for use in Download Sidebar', 'themedd' ); ?></em></p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'themedd' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'published' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'published' ) ); ?>" <?php checked( $instance['published'], true ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'published' ) ); ?>"><?php _e( 'Show date published', 'themedd' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'sales' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sales' ) ); ?>" <?php checked( $instance['sales'], true ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sales' ) ); ?>"><?php _e( 'Show number of sales', 'themedd' ); ?></label>
		</p>

		<?php if ( themedd_is_edd_sl_active() ) : ?>
		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'version' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'version' ) ); ?>" <?php checked( $instance['version'], true ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'version' ) ); ?>"><?php _e( 'Show version number', 'themedd' ); ?></label>
		</p>
		<?php endif; ?>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'categories' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'categories' ) ); ?>" <?php checked( $instance['categories'], true ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'categories' ) ); ?>"><?php _e( 'Show categories', 'themedd' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'tags' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tags' ) ); ?>" <?php checked( $instance['tags'], true ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tags' ) ); ?>"><?php _e( 'Show tags', 'themedd' ); ?></label>
		</p>

		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance               = $old_instance;
		$instance['title']      = ! empty( $new_instance['title'] )      ? strip_tags( $new_instance['title'] ) : '';
		$instance['published']  = ! empty( $new_instance['published'] )  ? true : false;
		$instance['sales']      = ! empty( $new_instance['sales'] )      ? true : false;
		$instance['categories'] = ! empty( $new_instance['categories'] ) ? true : false;
		$instance['tags']       = ! empty( $new_instance['tags'] )       ? true : false;

		if ( themedd_is_edd_sl_active() ) {
			$instance['version'] = ! empty( $new_instance['version'] ) ? true : false;
		}

		return $instance;

	}

}

/**
 * Register the widget.
 *
 * @since 1.0.0
 */
function themedd_register_widget_download_details() {
	register_widget( 'Themedd_Download_Details' );
}
add_action( 'widgets_init', 'themedd_register_widget_download_details' );
