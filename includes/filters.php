<?php
/**
 * Adds custom classes to the array of body classes.
 *
 * @since 1.0
 */
function themedd_body_classes( $classes ) {

	// Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}

	if (
		! is_active_sidebar( 'sidebar-1' ) && ! is_singular( 'download' ) ||
		! apply_filters( 'themedd_show_sidebar', true ) ||
		is_page_template( 'page-templates/no-sidebar.php' ) ||
		is_page_template( 'page-templates/slim.php' ) // The slim template also removes the sidebar
	) {
		$classes[] = 'no-sidebar';
	}

	// Add "slim" body class when using the page template
	if ( is_page_template( 'page-templates/slim.php' ) ) {
		$classes[] = 'slim';
	}

	return $classes;

}
add_filter( 'body_class', 'themedd_body_classes' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a 'Continue reading' link.
 *
 * @since 1.0.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
if ( ! function_exists( 'themedd_excerpt_more' ) ) :
function themedd_excerpt_more( $link ) {

	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf( '<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>',
		esc_url( get_permalink( get_the_ID() ) ),
		sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'themedd' ), get_the_title( get_the_ID() ) )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'themedd_excerpt_more' );
endif;
