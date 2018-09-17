<?php
/**
 * Genesis Starter
 *
 * This file adds functions to the Genesis Starter Theme.
 *
 * @package 	Genesis Starter
 * @author  	Lyketil [Digital Lab]
 * @license 	GPL-2.0+
 * @copyright   Copyright (c) 2018, Bruno Boehm
 * @link    	https://lyketil.com
 */


// Change this to your theme text domain, used for internationalising strings
$theme_text_domain = 'genesis-starter';


add_action( 'after_setup_theme', 'ly_i18n_setup' );
/**
 * Load the child theme textdomain for internationalization.
 *
 * Must be loaded before Genesis Framework /lib/init.php is included.
 * Translations can be filed in the /languages/ directory.
 *
 * @since 1.0.0
 */
function ly_i18n_setup() {
    load_child_theme_textdomain( $theme_text_domain, get_stylesheet_directory() . '/languages' );
}


// Starts the engine: default genesis way not used here since using a setup function
// require_once get_template_directory() . '/lib/init.php';


/**
 * Theme Setup
 * @since 1.0.0
 *
 * This setup function attaches all of the site-wide functions 
 * to the correct hooks and filters. All the functions attached
 * are defined below this setup function.
 *
 */

add_action('genesis_setup','ly_theme_setup', 15);

function ly_theme_setup() {
	// Defines the child theme (do not remove).
	define( 'CHILD_THEME_NAME', 'Lyketil Genesis Starter' );
	define( 'CHILD_THEME_URL', 'https://lyketil.com' );
	define( 'CHILD_THEME_VERSION', '1.0.0' );

	// Sets up the Theme.
	require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

	// Adds helper functions.
	require_once get_stylesheet_directory() . '/lib/helper-functions.php';

	// Adds image upload and color select to Customizer.
	require_once get_stylesheet_directory() . '/lib/customize.php';

	// Includes Customizer CSS.
	require_once get_stylesheet_directory() . '/lib/output.php';

	// Adds WooCommerce support.
	require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

	// Adds the required WooCommerce styles and Customizer CSS.
	require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

	// Adds the Genesis Connect WooCommerce notice.
	require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

	add_action( 'wp_enqueue_scripts', 'ly_enqueue_scripts_styles' );

	// Sets the content width based on the theme's design and stylesheet.
	if ( ! isset( $content_width ) ) {
		$content_width = 702; // Pixels.
	}

	// Adds support for HTML5 markup structure.
	add_theme_support(
		'html5', array(
			'caption',
			'comment-form',
			'comment-list',
			'gallery',
			'search-form',
		)
	);

	// Adds support for accessibility.
	add_theme_support(
		'genesis-accessibility', array(
			'404-page',
			'drop-down-menu',
			'headings',
			'rems',
			'search-form',
			'skip-links',
		)
	);

	// Adds viewport meta tag for mobile browsers.
	add_theme_support(
		'genesis-responsive-viewport'
	);

	// Adds custom logo in Customizer > Site Identity.
	add_theme_support(
		'custom-logo', array(
			'height'      => 120,
			'width'       => 700,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Renames primary and secondary navigation menus.
	add_theme_support(
		'genesis-menus', array(
			'primary'   => __( 'Header Menu', 'genesis-starter' ),
			'secondary' => __( 'Footer Menu', 'genesis-starter' ),
		)
	);

	// Adds support for after entry widget.
	add_theme_support( 'genesis-after-entry-widget-area' );

	// Adds support for 3-column footer widgets.
	add_theme_support( 'genesis-footer-widgets', 3 );

	// Removes header right widget area.
	unregister_sidebar( 'header-right' );

	// Removes secondary sidebar.
	unregister_sidebar( 'sidebar-alt' );

	// Removes site layouts.
	genesis_unregister_layout( 'content-sidebar-sidebar' );
	genesis_unregister_layout( 'sidebar-content-sidebar' );
	genesis_unregister_layout( 'sidebar-sidebar-content' );

	// Removes output of primary navigation right extras.
	remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
	remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

	add_action( 'genesis_theme_settings_metaboxes', 'ly_remove_metaboxes' );

	add_filter( 'genesis_customizer_theme_settings_config', 'ly_remove_customizer_settings' );

	// Displays custom logo.
	add_action( 'genesis_site_title', 'the_custom_logo', 0 );

	// Repositions primary navigation menu
	remove_action( 'genesis_after_header', 'genesis_do_nav' );
	add_action( 'genesis_header', 'genesis_do_nav', 12 );

	// Repositions the secondary navigation menu to the footer
	remove_action( 'genesis_after_header', 'genesis_do_subnav' );
	add_action( 'genesis_footer', 'genesis_do_subnav', 10 );

	add_filter( 'wp_nav_menu_args', 'ly_secondary_menu_args' );

	add_filter( 'genesis_author_box_gravatar_size', 'ly_author_box_gravatar' );

	add_filter( 'genesis_comment_list_args', 'ly_comments_gravatar' );

}


/**
 * Enqueues scripts and styles.
 *
 * @since 1.0.0
 */
function ly_enqueue_scripts_styles() {

	wp_enqueue_style(
		'genesis-starter-fonts',
		'//fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,600,700',
		array(),
		CHILD_THEME_VERSION
	);
	wp_enqueue_style( 'dashicons' );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script(
		'genesis-starter-responsive-menu',
		get_stylesheet_directory_uri() . "/js/responsive-menus{$suffix}.js",
		array( 'jquery' ),
		CHILD_THEME_VERSION,
		true
	);

	wp_localize_script(
		'genesis-starter-responsive-menu',
		'genesis_responsive_menu',
		ly_responsive_menu_settings()
	);

	wp_enqueue_script(
		'genesis-starter',
		get_stylesheet_directory_uri() . '/js/base.js',
		array( 'jquery' ),
		CHILD_THEME_VERSION,
		true
	);

}

/**
 * Defines responsive menu settings.
 *
 * @since 2.3.0 of Genesis Sample
 */
function ly_responsive_menu_settings() {

	$settings = array(
		'mainMenu'         => __( 'Menu', 'genesis-starter' ),
		'menuIconClass'    => 'dashicons-before dashicons-menu',
		'subMenu'          => __( 'Submenu', 'genesis-starter' ),
		'subMenuIconClass' => 'dashicons-before dashicons-arrow-down-alt2',
		'menuClasses'      => array(
			'combine' => array(
				'.nav-primary',
			),
			'others'  => array(),
		),
	);

	return $settings;

}


/**
 * Removes output of unused admin settings metaboxes.
 *
 * @since 2.6.0 of Genesis Starter
 *
 * @param string $_genesis_admin_settings The admin screen to remove meta boxes from.
 */
function ly_remove_metaboxes( $_genesis_admin_settings ) {

	remove_meta_box( 'genesis-theme-settings-header', $_genesis_admin_settings, 'main' );
	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_admin_settings, 'main' );

}


/**
 * Removes output of header settings in the Customizer.
 *
 * @since 2.6.0 of Genesis Sample
 *
 * @param array $config Original Customizer items.
 * @return array Filtered Customizer items.
 */
function ly_remove_customizer_settings( $config ) {

	unset( $config['genesis']['sections']['genesis_header'] );
	return $config;

}


/**
 * Reduces secondary navigation menu to one level depth.
 *
 * @since 2.2.3 of Genesis Sample
 *
 * @param array $args Original menu options.
 * @return array Menu options with depth set to 1.
 */
function ly_secondary_menu_args( $args ) {

	if ( 'secondary' !== $args['theme_location'] ) {
		return $args;
	}

	$args['depth'] = 1;
	return $args;

}


/**
 * Modifies size of the Gravatar in the author box.
 *
 * @since 2.2.3 of Genesis Sample
 *
 * @param int $size Original icon size.
 * @return int Modified icon size.
 */
function ly_author_box_gravatar( $size ) {

	return 90;

}


/**
 * Modifies size of the Gravatar in the entry comments.
 *
 * @since 2.2.3 of Genesis Sample
 *
 * @param array $args Gravatar settings.
 * @return array Gravatar settings with modified size.
 */
function ly_comments_gravatar( $args ) {

	$args['avatar_size'] = 60;
	return $args;

}