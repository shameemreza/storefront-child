<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront-child
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action( 'storefront_before_site' ); ?>

<div id="page" class="hfeed site">
	<?php do_action( 'storefront_before_header' ); ?>

	<header id="masthead" class="site-header" role="banner">
		<div class="col-full">
			<!-- Site branding / logo -->
			<div class="site-branding">
				<?php
				if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
					the_custom_logo();
				} else {
					?>
					<div class="site-branding-text">
						<h1 class="site-title">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php
								$blogname = get_bloginfo( 'name' );
								// Highlight 'Ninja' for animation if present
								$blogname = preg_replace( '/(Ninja)/i', '<span class="logo-ninja">$1</span>', esc_html( $blogname ) );
								echo $blogname;
								?>
							</a>
						</h1>
					</div>
					<?php
				}
				// Site tagline/description, visible on desktop only
				$description = get_bloginfo( 'description', 'display' );
				if ( $description ) :
					?>
					<span class="site-description desktop-only"><?php echo esc_html( $description ); ?></span>
				<?php endif; ?>
			</div>

			<!-- Main Navigation -->
			<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'storefront' ); ?>">
				<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false">
					<span class="hamburger-icon">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
							<rect y="4" width="24" height="2" rx="1" fill="currentColor"/>
							<rect y="11" width="24" height="2" rx="1" fill="currentColor"/>
							<rect y="18" width="24" height="2" rx="1" fill="currentColor"/>
						</svg>
					</span>
					<span class="menu-text"><?php echo esc_html( apply_filters( 'storefront_menu_toggle_text', __( 'Menu', 'storefront' ) ) ); ?></span>
				</button>
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => '',
							'menu_class'     => 'menu',
						)
					);
				} else {
					echo '<ul class="menu">';
					wp_list_pages( array( 'title_li' => '' ) );
					echo '</ul>';
				}
				?>
			</nav><!-- #site-navigation -->
		</div>
	</header><!-- #masthead -->

	<?php
	/**
	 * Functions hooked in to storefront_before_content
	 *
	 * @hooked storefront_header_widget_region - 10
	 * @hooked woocommerce_breadcrumb - 10
	 */
	do_action( 'storefront_before_content' );
	?>

	<div id="content" class="site-content" tabindex="-1">
		<div class="col-full">

		<?php
		do_action( 'storefront_content_top' ); 