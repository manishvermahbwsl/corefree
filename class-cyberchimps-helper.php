<?php
/**
 * Class Cyberchimps Functions
 *
 * @since   2.1.0
 * @package iFeature theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cyberchimps_Helper' ) ) {

	/**
	 * Cyberchimps Functions
	 */
	class Cyberchimps_Helper {

		/**
		 * Cyberchimps_Functions constructor.
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', __CLASS__ . '::cyberchimps_core_scripts', 20 );
			add_action( 'woocommerce_before_main_content', __CLASS__ . '::cyberchimps_wrapper_start', 10 );
			add_action( 'woocommerce_after_main_content', __CLASS__ . '::cyberchimps_wrapper_end', 10 );
			add_action( 'wp', __CLASS__ . '::cyberchimps_create_layout' );
		}

		/**
		 * Get Cyberchimnps options.
		 *
		 * @param $name Name of the options
		 * @param bool                     $default
		 * @return bool
		 */
		public static function cyberchimps_get_option( $name, $default = false ) {
			$options = get_option( 'cyberchimps_options' );

			if ( isset( $options[ $name ] ) ) {
				return $options[ $name ];
			}

			return $default;
		}

		/**
		 * Enqueue core scripts and core styles.
		 */
		public static function cyberchimps_core_scripts() {
			global $post;

			// Define paths.
			$directory_uri  = get_template_directory_uri();
			$js_path        = $directory_uri . '/cyberchimps/lib/js/';
			$bootstrap_path = $directory_uri . '/cyberchimps/lib/bootstrap/';

			// set up slimbox for gallery images.
			if ( self::cyberchimps_get_option( 'gallery_lightbox', 1 ) ) {
				wp_enqueue_script( 'gallery-lightbox', $js_path . 'gallery-lightbox.min.js', array( 'jquery' ), '1.0' );
			}

			// Load JS for slimbox.
			wp_enqueue_script( 'slimbox', $js_path . 'jquery.slimbox.min.js', array( 'jquery' ), '1.0' );

			// Load library for jcarousel.
			wp_enqueue_script( 'jcarousel', $js_path . 'jquery.jcarousel.min.js', array( 'jquery' ), '1.0' );

			// touch swipe gestures.
			wp_enqueue_script( 'jquery-mobile-touch', $js_path . 'jquery.mobile.custom.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'slider-call', $js_path . 'swipe-call.min.js', array( 'jquery', 'jquery-mobile-touch' ) );

			// Load Bootstrap Library Items.
			wp_enqueue_style( 'bootstrap-style', $bootstrap_path . 'css/bootstrap.min.css', false, '2.0.4' );
			wp_enqueue_style( 'bootstrap-responsive-style', $bootstrap_path . 'css/bootstrap-responsive.min.css', array( 'bootstrap-style' ), '2.0.4' );
			wp_enqueue_style( 'font-awesome', $directory_uri . '/cyberchimps/lib/css/font-awesome.min.css' );
			wp_enqueue_script( 'bootstrap-js', $bootstrap_path . 'js/bootstrap.min.js', array( 'jquery' ), '2.0.4', true );

			// Responsive design.
			if ( self::cyberchimps_get_option( 'responsive_design', 'checked' ) ) {
				wp_enqueue_style( 'cyberchimps_responsive', $directory_uri . '/cyberchimps/lib/bootstrap/css/cyberchimps-responsive.min.css', array( 'bootstrap-responsive-style', 'bootstrap-style' ), '1.0' );
			} else {
				wp_dequeue_style( 'cyberchimps_responsive' );
			}

			// Load core JS.
			wp_enqueue_script( 'core-js', $js_path . 'core.min.js', array( 'jquery' ) );

			// Placeholder fix for IE8/9.
			if ( preg_match( '/(?i)msie [8-9]/', $_SERVER['HTTP_USER_AGENT'] ) ) {
				wp_enqueue_script( 'placeholder', $js_path . 'jquery.placeholder.js', array( 'jquery' ) );
			}

			/**
			 * With the use of @2x at the end of an image it will use that to display the retina image. Both images have to been in the same folder
			 */
			wp_enqueue_script( 'retina-js', $js_path . 'retina-1.1.0.min.js', '', '1.1.0', true );

			// Load Core Stylesheet.
			wp_enqueue_style( 'core-style', $directory_uri . '/cyberchimps/lib/css/core.css', array( 'bootstrap-responsive-style', 'bootstrap-style' ), '1.0' );

			// Load Theme Stylesheet.
			wp_enqueue_style( 'style', get_stylesheet_uri(), array( 'core-style' ), '1.0' );

			// Add javascript for comments.
			if ( is_singular() ) {
				wp_enqueue_script( 'comment-reply' );
			}

			if ( self::cyberchimps_get_option( 'responsive_videos' ) == '1' ) {
				wp_enqueue_script( 'video', $js_path . 'video.min.js' );
			}

		}

		/**
		 * Wrapper.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_wrapper_start() {
			?>
			<div id="cc_woocommerce" class="container-full-width">

			<div class="container">

			<div class="container-fluid">

			<div id="container" <?php self::cyberchimps_filter_container_class(); ?>>

			<?php do_action( 'cyberchimps_before_content_container' ); ?>

			<div id="content" <?php self::cyberchimps_filter_content_class(); ?>>

			<?php
			do_action( 'cyberchimps_before_content' );
		}

		/**
		 * Calling action to be done before including header.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_before_header() {
			do_action( 'cyberchimps_before_header' );
		}

		/**
		 * Getting content for left sidebar.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_add_sidebar_left() {
			get_sidebar( 'left' );
		}

		/**
		 * Getting content for right sidebar.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_add_sidebar_right() {
			get_sidebar( 'right' );
		}

		/**
		 * Print out container class by applying filter.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_filter_container_class() {
			// Separates classes with a single space, collates classes for content element
			echo 'class="' . esc_attr( join( ' ', apply_filters( 'cyberchimps_container_class', array() ) ) ) . '"';
		}

		/**
		 * Print out content class by applying filter.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_filter_content_class() {
			// Separates classes with a single space, collates classes for content element.
			echo 'class="' . esc_attr( join( ' ', apply_filters( 'cyberchimps_content_class', array() ) ) ) . '"';
		}

		/**
		 * Cyberchimps wrapper end.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_wrapper_end() {
			?>
			<?php do_action( 'cyberchimps_after_content' ); ?>

			</div><!-- #content -->

			<?php do_action( 'cyberchimps_after_content_container' ); ?>

			</div><!-- #container .row-fluid-->

			</div><!-- container fluid -->

			</div><!-- conatiner -->

			</div><!-- container full width -->
			<?php
		}

		/**
		 * Getting content for left sidebar.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_create_layout() {
			global $post;

			if ( is_single() ) {
				$layout_type = self::cyberchimps_get_option( 'single_post_sidebar_options', 'right_sidebar' );

			} elseif ( is_home() ) {
				$layout_type = self::cyberchimps_get_option( 'sidebar_images', 'right_sidebar' );

			} elseif ( is_page() ) {
				$page_sidebar = get_post_meta( $post->ID, 'cyberchimps_page_sidebar' );
				$layout_type  = ( isset( $page_sidebar[0] ) ) ? $page_sidebar[0] : 'right_sidebar';

			} elseif ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_woocommerce() && is_shop() ) {
				$page_sidebar = get_post_meta( wc_get_page_id( 'shop' ), 'cyberchimps_page_sidebar' );
				$layout_type  = ( isset( $page_sidebar[0] ) ) ? $page_sidebar[0] : 'right_sidebar';

			} elseif ( is_archive() ) {
				$layout_type = self::cyberchimps_get_option( 'archive_sidebar_options', 'right_sidebar' );

			} elseif ( is_search() ) {
				$layout_type = self::cyberchimps_get_option( 'search_sidebar_options', 'right_sidebar' );

			} elseif ( is_404() ) {
				$layout_type = self::cyberchimps_get_option( 'error_sidebar_options', 'right_sidebar' );

			} else {
				$layout_type = apply_filters( 'cyberchimps_default_layout', 'right_sidebar' );
			}

			self::cyberchimps_get_layout( $layout_type );
		}

		/**
		 * Getting content for left sidebar.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_get_layout( $layout_type ) {
			$wide_sidebar = self::cyberchimps_get_option( 'wide_sidebar', 0 );
			$layout_type  = ( $layout_type ) ? $layout_type : 'right_sidebar';
			$content_span = ( 1 === $wide_sidebar ) ? __CLASS__ . '::cyberchimps_class_span8' : __CLASS__ . '::cyberchimps_class_span9';
			$sidebar_span = ( 1 === $wide_sidebar ) ? __CLASS__ . '::cyberchimps_class_span4' : __CLASS__ . '::cyberchimps_class_span3';

			switch ( $layout_type ) {
				case 'full_width':
					add_filter( 'cyberchimps_content_class', __CLASS__ . '::cyberchimps_class_span12' );
					break;
				case 'right_sidebar':
					add_action( 'cyberchimps_after_content_container', __CLASS__ . '::cyberchimps_add_sidebar_right' );
					add_filter( 'cyberchimps_content_class', $content_span );
					add_filter( 'cyberchimps_content_class', __CLASS__ . '::cyberchimps_content_sbr_class' );
					add_filter( 'cyberchimps_sidebar_right_class', $sidebar_span );
					break;
				case 'left_sidebar':
					add_action( 'cyberchimps_before_content_container', __CLASS__ . '::cyberchimps_add_sidebar_left' );
					add_filter( 'cyberchimps_content_class', $content_span );
					add_filter( 'cyberchimps_content_class', __CLASS__ . '::cyberchimps_content_sbl_class' );
					add_filter( 'cyberchimps_sidebar_left_class', $sidebar_span );
					break;
				case 'content_middle':
					add_action( 'cyberchimps_before_content_container', __CLASS__ . '::cyberchimps_add_sidebar_left' );
					add_action( 'cyberchimps_after_content_container', __CLASS__ . '::cyberchimps_add_sidebar_right' );
					add_filter( 'cyberchimps_content_class', __CLASS__ . '::cyberchimps_class_span6' );
					add_filter( 'cyberchimps_content_class', __CLASS__ . '::cyberchimps_content_sb2_class' );
					add_filter( 'cyberchimps_sidebar_left_class', __CLASS__ . '::cyberchimps_class_span3' );
					add_filter( 'cyberchimps_sidebar_right_class', __CLASS__ . '::cyberchimps_class_span3' );
					break;
				case 'left_right_sidebar':
					add_action( 'cyberchimps_after_content_container', __CLASS__ . '::cyberchimps_add_sidebar_left' );
					add_action( 'cyberchimps_after_content_container', __CLASS__ . '::cyberchimps_add_sidebar_right' );
					add_filter( 'cyberchimps_content_class', __CLASS__ . '::cyberchimps_class_span6' );
					add_filter( 'cyberchimps_content_class', __CLASS__ . '::cyberchimps_content_sb2r_class' );
					add_filter( 'cyberchimps_sidebar_left_class', __CLASS__ . '::cyberchimps_class_span3' );
					add_filter( 'cyberchimps_sidebar_right_class', __CLASS__ . '::cyberchimps_class_span3' );
					break;
			}
		}

		/**
		 * Content span12.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span12( $classes ) {
			$classes[] = 'span12';

			return $classes;
		}

		/**
		 * Content span11.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span11( $classes ) {
			$classes[] = 'span11';

			return $classes;
		}

		/**
		 * Content span10.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span10( $classes ) {
			$classes[] = 'span10';

			return $classes;
		}

		/**
		 * Content span9.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span9( $classes ) {
			$classes[] = 'span9';

			return $classes;
		}

		/**
		 * Content span8.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span8( $classes ) {
			$classes[] = 'span8';

			return $classes;
		}

		/**
		 * Content span7.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span7( $classes ) {
			$classes[] = 'span7';

			return $classes;
		}

		/**
		 * Content span6.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span6( $classes ) {
			$classes[] = 'span6';

			return $classes;
		}

		/**
		 * Content span5.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span5( $classes ) {
			$classes[] = 'span5';

			return $classes;
		}

		/**
		 * Content span4.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span4( $classes ) {
			$classes[] = 'span4';

			return $classes;
		}

		/**
		 * Content span3.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span3( $classes ) {
			$classes[] = 'span3';

			return $classes;
		}

		/**
		 * Content span2.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span2( $classes ) {
			$classes[] = 'span2';

			return $classes;
		}

		/**
		 * Content span1.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_span1( $classes ) {
			$classes[] = 'span1';

			return $classes;
		}

		/**
		 * Setting content classes for different sidebars
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_content_sbr_class( $classes ) {
			$classes[] = 'content-sidebar-right';

			return $classes;
		}

		/**
		 * Left sidebar.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_content_sbl_class( $classes ) {
			$classes[] = 'content-sidebar-left';

			return $classes;
		}

		/**
		 * Content sidebar.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_content_sb2_class( $classes ) {
			$classes[] = 'content-sidebar-2';

			return $classes;
		}

		/**
		 * Right sidebar.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_content_sb2r_class( $classes ) {
			$classes[] = 'content-sidebar-2-right';

			return $classes;
		}

		/**
		 * Container fluid.
		 *
		 * @since  8.0.0
		 */
		/* Start of functions to add different classes for html elements to $classes[] */
		public static function cyberchimps_class_container_fluid( $classes ) {
			$classes[] = 'container-fluid';

			return $classes;
		}

		/**
		 * Raw fluid.
		 *
		 * @since  8.0.0
		 */
		public static function cyberchimps_class_row_fluid( $classes ) {
			$classes[] = 'row-fluid';

			return $classes;
		}

		// Sets fallback menu for 1 level. Could use preg_split to have children displayed too
		public static function cyberchimps_fallback_menu() {
			$walker  = new Cyberchimps_fallback_walker();
			$args    = array(
				'depth'       => 0,
				'show_date'   => '',
				'date_format' => '',
				'child_of'    => 0,
				'exclude'     => '',
				'include'     => '',
				'title_li'    => '',
				'echo'        => 0,
				'authors'     => '',
				'sort_column' => 'menu_order, post_title',
				'link_before' => '',
				'link_after'  => '',
				'walker'      => $walker,
				'post_type'   => 'page',
				'post_status' => 'publish',
			);
			$pages   = wp_list_pages( $args );
			$prepend = '<ul id="menu-menu" class="nav">';
			$pages   = apply_filters( 'cyberchimps_fallback_menu_filter', $pages, $args );
			$append  = '</ul>';
			$output  = $prepend . $pages . $append;
			echo $output;
		}

	}
	new Cyberchimps_Helper();
}
