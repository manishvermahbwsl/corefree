<?php
/**
 * Ifeature Theme
 *
 * @since   2.1.0
 * @package iFeature theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cyberchimps_Hooks' ) ) {

	/**
	 * Cyberchimps Hooks
	 */
	class Cyberchimps_Hooks {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'cyberchimps_blog_content', __CLASS__ . '::cyberchimps_blog_section_order_action' );
			add_action( 'blog_post_page', __CLASS__ . '::cyberchimps_post' );
			add_action( 'cyberchimps_before_content', __CLASS__ . '::cyberchimps_blog_title' );
			add_action( 'cyberchimps_footer', __CLASS__ . '::cyberchimps_footer_credit' );
			add_filter( 'dynamic_sidebar_params', __CLASS__ . '::cyberchimps_footer_widgets' );
			add_action( 'cyberchimps_header', __CLASS__ . '::cyberchimps_header_section_order' );
			add_action( 'cyberchimps_header_content', __CLASS__ . '::cyberchimps_logo_icons' );
			add_action( 'cyberchimps_header_content', __CLASS__ . '::cyberchimps_logo_searchform' );
			add_action( 'cyberchimps_description_icons', __CLASS__ . '::cyberchimps_description_icons' );
			add_action( 'cyberchimps_sitename_contact', __CLASS__ . '::cyberchimps_sitename_contact' );
			add_action( 'cyberchimps_logo_description', __CLASS__ . '::cyberchimps_logo_description' );
			add_action( 'cyberchimps_logo', __CLASS__ . '::cyberchimps_logo' );
			add_action( 'cyberchimps_sitename_register', __CLASS__ . '::cyberchimps_logo_register_content' );
			add_action( 'cyberchimps_banner', __CLASS__ . '::cyberchimps_banner_content' );
			add_action( 'cyberchimps_page_content', __CLASS__ . '::cyberchimps_page_section_order_action' );
			add_action( 'page_section', __CLASS__ . '::cyberchimps_page' );
			add_action( 'wp_head', __CLASS__ . '::cyberchimps_css_styles', 50 );
			add_action( 'wp_enqueue_scripts', __CLASS__ . '::cyberchimps_skin_styles', 35 );
			add_action( 'wp_head', __CLASS__ . '::cyberchimps_header_scripts' );
		}

		/**
		 * Blog content order.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_blog_section_order_action() {
			global $post;

			$defaults = array();
			$default  = apply_filters(
				'cyberchimps_elements_draganddrop_defaults',
				array(
					'blog_post_page' => __( 'Post Page', 'cyberchimps_core' ),
				)
			);
			foreach ( $default as $key => $val ) {
				$defaults[] = $key;
			}

			$blog_section_order = Cyberchimps_Helper::cyberchimps_get_option( 'blog_section_order', $defaults );
			// select default in case options are empty.
			$blog_section_order = ( '' === $blog_section_order ) ? array( 'blog_post_page' ) : $blog_section_order;
			$slider_size        = Cyberchimps_Helper::cyberchimps_get_option( 'blog_slider_size', 'full' );
			if ( is_array( $blog_section_order ) ) {

				// Check if both of slider and blog post were active.
				if ( in_array( 'page_slider', $blog_section_order, true ) && in_array( 'blog_post_page', $blog_section_order, true ) ) {

					// Get position of slider and blog post page in the active elements list.
					$position_slider    = array_search( 'page_slider', $blog_section_order, true );
					$position_blog_post = array_search( 'blog_post_page', $blog_section_order, true );

					$slider_order = $position_slider > $position_blog_post ? 'after' : 'before';
					cyberchimps_add_half_slider_action( $slider_order );
				}

				foreach ( $blog_section_order as $func ) {
					// checks if slider is selected at half size, if it is it removes it so we can display it above blog content.
					if ( 'page_slider' === $func && 'half' === $slider_size ) {
						$func = '';
					} else {
						?>
						<div class="container-full-width" id="<?php echo $func; ?>_section">
							<div class="container">
								<div class="container-fluid">
									<?php
									do_action( $func );
									?>
								</div>
								<!-- .container-fluid-->
							</div>
							<!-- .container -->
						</div>    <!-- .container-full-width -->
						<?php
					}
				}
			}
		}

		/**
		 * Blog posts.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_post() {

			?>
			<div id="container" <?php Cyberchimps_Helper::cyberchimps_filter_container_class(); ?>>

				<?php do_action( 'cyberchimps_before_content_container' ); ?>

				<div id="content" <?php Cyberchimps_Helper::cyberchimps_filter_content_class(); ?>>

					<?php do_action( 'cyberchimps_before_content' ); ?>

					<?php if ( have_posts() ) : ?>

						<?php
						while ( have_posts() ) :
							the_post();
							?>

							<?php get_template_part( 'content', get_post_format() ); ?>

						<?php endwhile; ?>

					<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>

						<?php get_template_part( 'no-results', 'index' ); ?>

					<?php endif; ?>

					<?php do_action( 'cyberchimps_after_content' ); ?>

				</div>
				<!-- #content -->
				<?php do_action( 'cyberchimps_after_content_container' ); ?>

			</div><!-- #container -->
			<?php
		}

		/**
		 * Blog Title.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_blog_title() {
			if ( is_home() ) {
				// Add blog title if toggle is on.
				$title_toggle = Cyberchimps_Helper::cyberchimps_get_option( 'blog_title', false );
				if ( $title_toggle ) {
					$title_text = Cyberchimps_Helper::cyberchimps_get_option( 'blog_title_text', __( 'Our Blog', 'cyberchimps_core' ) );
					echo apply_filters(
						'cyberchimps_blog_title_html',
						'
        <div id="cyberchimps_blog_title" class="row-fluid">
            <header class="page-header">
                <h1 class="page-title">' . $title_text . '</h1>
            </header>
        </div>'
					);
				}
			}
		}

		/**
		 * Blog Footer credit.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_footer_credit() {
			?>
			<div class="container-full-width" id="after_footer">
				<div class="container">
					<div class="container-fluid">
						<footer class="site-footer row-fluid">
							<div class="span6">
								<div id="credit">
									<?php if ( Cyberchimps_Helper::cyberchimps_get_option( 'footer_cyberchimps_link', 1 ) === '1' ) : ?>
										<a href="http://cyberchimps.com/" target="_blank" title="CyberChimps Themes">
										<?php if ( 'free' === cyberchimps_theme_check() ) { ?>
											<h4 class="cc-credit-text">CyberChimps WordPress Themes</h4></a>
											<?php
										} else {
											?>

											<img width="32" height="32" class="cc-credit-logo" src="<?php echo get_template_directory_uri(); ?>/cyberchimps/lib/images/achimps.png" alt="CyberChimps"/>
											<h4 class="cc-credit-text"><span>Cyber</span>Chimps</h4></a>
											<div class="market" style="line-height:2.3"><a href="http://neilpatel.com/" rel="noindex, nofollow">Marketed  By Neil Patel</a></div>
										<?php } ?>

									<?php endif; ?>

								</div>
							</div>
							<!-- Adds the afterfooter copyright area -->
							<div class="span6">
								<?php $copyright = ( Cyberchimps_Helper::cyberchimps_get_option( 'footer_copyright_text' ) ) ? Cyberchimps_Helper::cyberchimps_get_option( 'footer_copyright_text' ) : 'CyberChimps &#169;' . date( 'Y' ); ?>
								<div id="copyright">
									<?php echo wp_kses_post( $copyright ); ?>
								</div>
							</div>
						</footer>
						<!-- row-fluid -->
					</div>
					<!-- .container-fluid-->
				</div>
				<!-- .container -->
			</div>    <!-- #after_footer -->
			<?php
		}

		/**
		 * Footer widgets.
		 *
		 * @since  2.0.0
		 * Start new row of footer widgets with a new row-fluid div so that it keeps the fluid layout.
		 * @param array $params Parameters.
		 */
		public static function cyberchimps_footer_widgets( $params ) {

			// Checked if it's footer widgets.
			if ( 'Footer Widgets' === $params[0]['name'] ) {

				// Declare a widget counter globally so that we can increase it in each iteration.
				global $footer_widget_counter;
				$footer_widget_counter++;

				// If it's 5(or multiple of 5)th widget then we need to close the current row-fluid div and start a new one.
				if ( 0 === $footer_widget_counter % 5 ) {
					echo '</div> <div class="row-fluid">';
				}
			}

			return $params;
		}


		/**
		 * Header Section.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_header_section_order() {
			// get the defaults from the themes function file and turn the key into the value in a new array to mirror what happens within the theme when their are options saved in the database.
			$defaults = array();
			$default  = apply_filters( 'cyberchimps_header_drag_and_drop_default', array( 'cyberchimps_header_content' => __( 'Logo + Icons', 'cyberchimps_core' ) ) );
			foreach ( $default as $key => $val ) {
				$defaults[] = $key;
			}
			// call the database results and if they don't exist then call the defaults from above.
			$header_section = Cyberchimps_Helper::cyberchimps_get_option( 'header_section_order', $defaults );
			$header_section = ( '' === $header_section ) ? $defaults : $header_section;

			if ( is_array( $header_section ) ) {
				foreach ( $header_section as $func ) {
					do_action( $func );
				}
			}
		}

		/**
		 * Logo/Icons header element.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_logo_icons() {
			?>
			<header id="cc-header" class="row-fluid">
				<div class="span7">
					<?php
						self::cyberchimps_header_logo();
					?>
				</div>

				<div id="register" class="span5">
					<?php

						self::cyberchimps_header_social_icons();

					?>
				</div>
			</header>
			<?php
		}


		/**
		 * Logo/Search header element.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_logo_searchform() {
			?>
			<header id="cc-header" class="row-fluid">
				<div class="span7">
					<?php
						self::cyberchimps_header_logo();
					?>
				</div>

				<div id="search" class="span5">
					<?php
					get_search_form( true );
					?>
				</div>
			</header>
			<?php
		}

		/**
		 * Description/Icons header element.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_description_icons() {
			?>
			<header id="cc-header" class="row-fluid">
				<div class="span7">
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
				</div>

				<div id="register" class="span5">
					<?php
						self::cyberchimps_header_social_icons();
					?>
				</div>
			</header>
			<?php
		}


		/**
		 * Logo and Contact.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_sitename_contact() {
			?>
			<header id="cc-header" class="row-fluid">
				<div class="span7">
					<?php
						self::cyberchimps_header_logo();
					?>
				</div>

				<div id="register" class="span5">
					<?php
						echo self::cyberchimps_contact_info();
					?>
				</div>
			</header>
			<?php
		}

		/**
		 * Logo and Description.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_logo_description() {
			?>
			<header id="cc-header" class="row-fluid">
				<div class="span7">
					<?php
						self::cyberchimps_header_logo();
					?>
				</div>

				<div id="description" class="span5">
					<?php echo cyberchimps_description(); ?>
				</div>
			</header>
			<?php
		}


		/**
		 * Defines action for header elelment "Logo".
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_logo() {
			?>
			<header id="cc-header" class="row-fluid">
				<div class="span7">
					<?php
						self::cyberchimps_header_logo();
					?>
				</div>
			</header>
			<?php
		}

		/**
		 * Header left content (sitename or logo).
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_header_logo() {

			$url = ( Cyberchimps_Helper::cyberchimps_get_option( 'custom_logo_url' ) === '1' ) ? Cyberchimps_Helper::cyberchimps_get_option( 'custom_logo_url_link' ) : esc_url( home_url() );
			if ( Cyberchimps_Helper::cyberchimps_get_option( 'custom_logo' ) === '1' ) {
				$logo = Cyberchimps_Helper::cyberchimps_get_option( 'custom_logo_uploader' );
				?>
				<div id="logo">
					<a href="<?php echo $url; ?>" title="<?php echo get_bloginfo( 'name' ); ?>"><img src="<?php echo stripslashes( $logo ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>"></a>
				</div>
				<?php
			} else {

					self::cyberchimps_header_site_title();

			}
		}

		/**
		 * Header site title.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_header_site_title() {
			?>
			<div class="hgroup">
				<h2 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
			</div>
			<?php
		}

		/**
		 * Social icons positioned in header and some theme's footer
		 *
		 * The key of the $social variable has to match the font icon you want to use. If that differs from the name you want displayed set the title key
		 * e.g. $social['twitterbird']['title'] = 'twitter';
		 *
		 * styling is located in /lib/css/core.css.
		 * icon fonts are from http://drinchev.github.io/monosocialiconsfont/.
		 */
		public static function cyberchimps_header_social_icons() {

			// get the design of the icons to apply the right class.
			$design = Cyberchimps_Helper::cyberchimps_get_option( 'theme_backgrounds', 'default' );

			// create array of social icons to loop through to check if they are set and add title key to.
			// social networks with names different to key.

			$social['twitterbird']['set']   = Cyberchimps_Helper::cyberchimps_get_option( 'social_twitter', 'checked' );
			$social['twitterbird']['title'] = 'twitter';
			$social['twitterbird']['url']   = Cyberchimps_Helper::cyberchimps_get_option( 'twitter_url' );
			$social['facebook']['set']      = Cyberchimps_Helper::cyberchimps_get_option( 'social_facebook', 'checked' );
			$social['facebook']['url']      = Cyberchimps_Helper::cyberchimps_get_option( 'facebook_url' );
			$social['googleplus']['set']    = Cyberchimps_Helper::cyberchimps_get_option( 'social_google', 'checked' );
			$social['googleplus']['url']    = Cyberchimps_Helper::cyberchimps_get_option( 'google_url' );
			$social['flickr']['set']        = Cyberchimps_Helper::cyberchimps_get_option( 'social_flickr' );
			$social['flickr']['url']        = Cyberchimps_Helper::cyberchimps_get_option( 'flickr_url' );
			$social['pinterest']['set']     = Cyberchimps_Helper::cyberchimps_get_option( 'social_pinterest' );
			$social['pinterest']['url']     = Cyberchimps_Helper::cyberchimps_get_option( 'pinterest_url' );
			$social['linkedin']['set']      = Cyberchimps_Helper::cyberchimps_get_option( 'social_linkedin' );
			$social['linkedin']['url']      = Cyberchimps_Helper::cyberchimps_get_option( 'linkedin_url' );
			$social['youtube']['set']       = Cyberchimps_Helper::cyberchimps_get_option( 'social_youtube' );
			$social['youtube']['url']       = Cyberchimps_Helper::cyberchimps_get_option( 'youtube_url' );
			$social['map']['set']           = Cyberchimps_Helper::cyberchimps_get_option( 'social_googlemaps' );
			$social['map']['title']         = 'google maps';
			$social['map']['url']           = Cyberchimps_Helper::cyberchimps_get_option( 'googlemaps_url' );
			$social['email']['set']         = Cyberchimps_Helper::cyberchimps_get_option( 'social_email' );
			$social['email']['url']         = 'mailto:' . Cyberchimps_Helper::cyberchimps_get_option( 'email_url' );
			$social['rss']['set']           = Cyberchimps_Helper::cyberchimps_get_option( 'social_rss' );
			$social['rss']['url']           = Cyberchimps_Helper::cyberchimps_get_option( 'rss_url' );
			$social['instagram']['set']     = Cyberchimps_Helper::cyberchimps_get_option( 'social_instagram' );
			$social['instagram']['url']     = Cyberchimps_Helper::cyberchimps_get_option( 'instagram_url' );
			$social['snapchat']['set']      = Cyberchimps_Helper::cyberchimps_get_option( 'social_snapchat' );
			$social['snapchat']['url']      = Cyberchimps_Helper::cyberchimps_get_option( 'snapchat_url' );

			$output = '';

			// get the blog title to add to link title.
			$link_title = get_bloginfo( 'title' );

			// Loop through the $social variable.
			foreach ( $social as $key => $value ) {

				// Check that the social icon has been set.
				if ( ! empty( $value['set'] ) ) {

					// check if title is set and use it otherwise use key as title.
					$title = ( isset( $social[ $key ]['title'] ) ) ? $social[ $key ]['title'] : $key;

					// Create the output.
					$output .= '<a href="' . esc_url( $social[ $key ]['url'] ) . '"' . ( 'email' !== $key ? ' target="_blank"' : '' )
						. ' title="' . esc_attr( $link_title . ' ' . ucwords( $title ) ) . '" class="symbol ' . $key . '"></a>';
				}
			}

			// Echo to the page.
			?>
			<div id="social">
				<div class="<?php echo $design; ?>-icons">
					<?php echo $output; ?>
				</div>
			</div>

			<?php
		}

		/**
		 * Custom HTML header element.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_custom_header_element_content() {
			?>
			<header id="cc-header" class="row-fluid">
				<div class="span7">
					<?php echo stripslashes( Cyberchimps_Helper::cyberchimps_get_option( 'custom_header_element' ) ); ?>
				</div>
			</header>
			<?php
		}

		/**
		 * Sitename/Register.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_logo_register_content() {
			// global $current_user; Commented By Swapnil as global $current_user is no longer being use.
			?>
			<header id="cc-header" class="row-fluid">
				<div class="span7">
					<?php
					if ( function_exists( 'cyberchimps_header_logo' ) ) {
						cyberchimps_header_logo();
					}
					?>
				</div>

				<div id="register" class="span5">
					<div class="register">
						<?php if ( ! is_user_logged_in() ) : ?>
							<?php wp_loginout(); ?> <?php wp_meta(); ?> | <?php wp_register( '', '', true ); ?>
						<?php else : ?>
							Welcome back <strong>
							<?php
								$current_user = wp_get_current_user();
								echo( $current_user->user_login );
							?>
								</strong> | <?php wp_loginout(); ?>
						<?php endif; ?>
					</div>
				</div>
			</header>
			<?php
		}


		/**
		 * Full-Width Logo.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_banner_content() {

			// Getting banner options.
			$banner  = Cyberchimps_Helper::cyberchimps_get_option( 'header_banner_image' );
			$default = get_template_directory_uri() . apply_filters( 'cyberchimps_banner_img', '/cyberchimps/lib/images/banner.jpg' );
			$url     = Cyberchimps_Helper::cyberchimps_get_option( 'header_banner_url' );
			?>
			<header id="cc-header" class="row-fluid">
				<div id="banner">
					<?php if ( '' !== $banner ) : ?>
						<a href="<?php echo $url; ?>"><img src="<?php echo $banner; ?>" alt="logo"></a>
					<?php endif; ?>
					<?php if ( '' === $banner ) : ?>
						<a href="<?php echo $url; ?>"><img src="<?php echo $default; ?>" alt="logo"></a>
					<?php endif; ?>
				</div>
			</header>
			<?php
		}

		/**
		 * Contact info.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_contact_info() {
			$contact = apply_filters( 'cyberchimps_header_contact', Cyberchimps_Helper::cyberchimps_get_option( 'contact_details' ) );
			?>

			<div class="contact_details">
				<?php echo $contact; ?>
			</div>
			<?php
		}

		/**
		 * Description.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_description() {
			$description = get_bloginfo( 'description' );
			?>
			<div class="blog-description">
				<p><?php echo $description; ?></p>
			</div>
			<?php
		}


		/**
		 * Page content order.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_page_section_order_action() {
			global $post;

			// Checking for password protection.
			if ( ! post_password_required() ) {
				$page_section_order = get_post_meta( $post->ID, 'cyberchimps_page_section_order', true );

				// set page default if nothing is selected.
				$page_section_order = ( '' === $page_section_order ) ? array( 'page_section' ) : $page_section_order;
				$slider_size        = get_post_meta( $post->ID, 'cyberchimps_slider_size', true );
				if ( is_array( $page_section_order ) ) {

					// Check if both of slider and page were active.
					if ( in_array( 'page_slider', $page_section_order, true ) && in_array( 'page_section', $page_section_order, true ) ) {

						// Get position of slider and blog post page in the active elements list.
						$position_slider = array_search( 'page_slider', $page_section_order, true );
						$position_page   = array_search( 'page_section', $page_section_order, true );

						$slider_order = $position_slider > $position_page ? 'after' : 'before';
						cyberchimps_add_half_slider_action( $slider_order );
					}

					foreach ( $page_section_order as $func ) {

						// checks if slider is selected at half size, if it is it removes it so we can display it above page content.
						if ( 'page_slider' === $func && 'half' === $slider_size ) {
							$func = '';
						} else {
							?>
							<div class="container-full-width" id="<?php echo $func; ?>_section">
								<div class="container">
									<div class="container-fluid">
										<?php
										do_action( $func );
										?>
									</div>
									<!-- .container-fluid-->
								</div>
								<!-- .container -->
							</div>    <!-- .container-full-width -->
							<?php
						}
					}
				}
			} else {
				// Get the form to submit password.
				?>
				<div class="container-full-width" id="<?php echo $func; ?>_section">
					<div class="container">
						<div class="container-fluid">
							<div id="container" class="row-fluid">
								<div id="content">
									<article class="post">
										<?php
										echo get_the_password_form();
										?>
									</article>
								</div>
							</div>
						</div>
						<!-- .container-fluid-->
					</div>
					<!-- .container -->
				</div>    <!-- .container-full-width -->
				<?php
			}
		}

		/**
		 * Blog page.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_page() {
			?>
			<div id="container" <?php Cyberchimps_Helper::cyberchimps_filter_container_class(); ?>>

				<?php do_action( 'cyberchimps_before_content_container' ); ?>

				<div id="content" <?php Cyberchimps_Helper::cyberchimps_filter_content_class(); ?>>

					<?php do_action( 'cyberchimps_before_content' ); ?>

					<?php
					while ( have_posts() ) :
						the_post();
						?>

						<?php get_template_part( 'content', 'page' ); ?>

						<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || '0' !== get_comments_number() ) {
							comments_template( '', true );
						}
						?>

					<?php endwhile; // end of the loop. ?>

					<?php do_action( 'cyberchimps_after_content' ); ?>

				</div>
				<!-- #content -->

				<?php do_action( 'cyberchimps_after_content_container' ); ?>

			</div><!-- #container .row-fluid-->
			<?php
		}

		/**
		 * Adds styles to header created from functions at the bottom.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_css_styles() {
			$body_styles      = self::cyberchimps_body_styles();
			$link_styles      = self::cyberchimps_link_styles();
			$container_styles = self::cyberchimps_layout_styles();
			$headings_styles  = self::cyberchimps_headings_styles();
			?>

			<style type="text/css" media="all">
				<?php if ( ! empty( $body_styles ) ) : ?>
				body {
					<?php
					// Changed to previous code for minor font changes.
					foreach ( $body_styles as $key => $body_style ) :
						?>
						<?php echo $key; ?> : <?php echo $body_style; ?>;
				<?php endforeach; ?>
				}

				<?php endif; ?>
				<?php if ( ! empty( $link_styles ) ) : ?>
					<?php foreach ( $link_styles as $key2 => $link_style ) : ?>
						<?php echo $key2; ?>
				{
					color:
						<?php echo $link_style; ?>
				;
				}
				<?php endforeach; ?>
				<?php endif; ?>
				<?php if ( ! empty( $container_styles ) ) : ?>
				.container {
					<?php
					foreach ( $container_styles as $key3 => $container_style ) :
						?>
						 <?php echo $key3; ?> : <?php echo $container_style; ?>px;
				<?php endforeach; ?>
				}

				<?php endif; ?>

				<?php if ( ! empty( $headings_styles ) ) { ?>
				h1, h2, h3, h4, h5, h6 {
					<?php
					foreach ( $headings_styles as $key => $headings_style ) {
						// Changed to previous code for minor font changes.
						if ( ! empty( $headings_style ) ) {
							echo $key;
							?>
					 : <?php echo $headings_style; ?>;
							<?php
						}
					}
					?>
				}

					<?php
				}

				$width = intval( Cyberchimps_Helper::cyberchimps_get_option( 'max_width' ) ) . 'px';
				if ( ! Cyberchimps_Helper::cyberchimps_get_option( 'responsive_design', 'checked' ) ) {
					?>
				@media screen and (max-width: <?php echo $width; ?>) {
					.container-full-width {
						width: <?php echo $width; ?>;
					}
				}
					<?php
				}

				?>

			</style>
			<?php
			return;
		}

		/**
		 * Creat headings_styles array from options.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_headings_styles() {

			// Set header font family.
			$headings_styles      = Cyberchimps_Helper::cyberchimps_get_option( 'font_family_headings' );
			$google_font_headings = Cyberchimps_Helper::cyberchimps_get_option( 'google_font_headings' );

			// older versions will have saved the font family as a string so we need to check for that first.
			if ( is_array( $headings_styles ) ) {
				$headings_styles['font-family'] = $headings_styles['face'];
			} else {
				$headings_styles                = array();
				$headings_styles['font-family'] = $headings_styles;
			}

			// Check if Google fonts have been selected.
			if ( 'Google Fonts' === $headings_styles['font-family'] && '' !== $google_font_headings ) {
				$headings_styles['font-family'] = $google_font_headings;

				// Check if SSL is present, if so then use https othereise use http.
				$protocol = is_ssl() ? 'https' : 'http';

				wp_register_style( 'google-font-headings', $protocol . '://fonts.googleapis.com/css?family=' . $google_font_headings );
				wp_enqueue_style( 'google-font-headings' );
			}

			// TODO recreate original settings so they are actually named by the css style they refer to
			// eg face becomes font-family, size is font-size etc.

			unset( $headings_styles['size'] );
			unset( $headings_styles['face'] );
			unset( $headings_styles['color'] );
			unset( $headings_styles['style'] );

			return $headings_styles;
		}

		/**
		 * Creates body_styles array from options.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_body_styles() {
			$body_styles = array();

			if ( Cyberchimps_Helper::cyberchimps_get_option( 'typography_options' ) ) {
				$typography_options = Cyberchimps_Helper::cyberchimps_get_option( 'typography_options' );
				// changes terminology for typography to css elements.
				foreach ( $typography_options as $option => $value ) {
					if ( 'size' === $option ) {
						$option = 'font-size';
					}
					if ( 'face' === $option ) {
						$option = 'font-family';
					}
					if ( 'style' === $option ) {
						$option = 'font-weight';
					}
					if ( '' !== $value ) {
						$body_styles[ $option ] = $value;
					}
				}
			}

			// Set font-family if google font is on.
			$google_font = Cyberchimps_Helper::cyberchimps_get_option( 'google_font_field' );

			if ( isset( $body_styles['font-family'] ) && 'Google Fonts' === $body_styles['font-family'] && '' !== $google_font ) {
				$body_styles['font-family'] = $google_font;

				// Check if SSL is present, if so then use https othereise use http.
				$protocol = is_ssl() ? 'https' : 'http';

				wp_register_style( 'google-font', $protocol . '://fonts.googleapis.com/css?family=' . $google_font );
				wp_enqueue_style( 'google-font' );
			}
			if ( Cyberchimps_Helper::cyberchimps_get_option( 'text_colorpicker' ) ) {
				$body_styles['color'] = Cyberchimps_Helper::cyberchimps_get_option( 'text_colorpicker' );
			}
			return $body_styles;
		}

		/**
		 * Creates link color array for just a tag.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_link_styles() {
			$link_styles = array();
			if ( Cyberchimps_Helper::cyberchimps_get_option( 'link_colorpicker' ) ) {
				$link_styles['a'] = Cyberchimps_Helper::cyberchimps_get_option( 'link_colorpicker' );
			}
			if ( Cyberchimps_Helper::cyberchimps_get_option( 'link_hover_colorpicker' ) ) {
				$link_styles['a:hover'] = Cyberchimps_Helper::cyberchimps_get_option( 'link_hover_colorpicker' );
			}

			return $link_styles;
		}

		/**
		 * Creates width for main container of website.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_layout_styles() {
			$container_styles = array();
			if ( Cyberchimps_Helper::cyberchimps_get_option( 'max_width' ) ) {
				$width = intval( Cyberchimps_Helper::cyberchimps_get_option( 'max_width' ) );
				$key   = ( Cyberchimps_Helper::cyberchimps_get_option( 'responsive_design', 'checked' ) ) ? 'max-width' : 'width';
				if ( $width < 400 || empty( $width ) ) {
					$container_styles[ $key ] = 1020;
				} else {
					$container_styles[ $key ] = $width;
				}
			}

			return $container_styles;
		}


		/**
		 * Add styles for skin selection.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_skin_styles() {
			$skins = Cyberchimps_Helper::cyberchimps_get_option( 'cyberchimps_skin_color' );
			$skin = '';
			if ( is_array( $skins ) ) {
				$skin_key = array_keys( $skins );
			} else {
				$skin = $skins;
			}
			if ( 'default' !== $skin ) {
				wp_enqueue_style( 'skin-style', get_template_directory_uri() . '/inc/css/skins/' . $skin . '.css', array( 'style' ), '1.0' );
			}
		}

		/**
		 * Add custom header scripts.
		 *
		 * @since  2.0.0
		 */
		public static function cyberchimps_header_scripts() {
			$header_scripts = Cyberchimps_Helper::cyberchimps_get_option( 'header_scripts' );
			echo $header_scripts;
		}


	}





	new Cyberchimps_Hooks();
}
?>
