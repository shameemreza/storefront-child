<?php
/**
 * Storefront Child Theme functions and definitions
 */

// Set up theme defaults and registers support for various WordPress features
function storefront_child_setup() {
    // Add custom logo support
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'storefront_child_setup', 11);

// Enqueue parent and child theme styles
function storefront_child_enqueue_styles() {
    // Enqueue parent theme style
    wp_enqueue_style('storefront-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('storefront')->get('Version')
    );
    
    // Enqueue fonts
    wp_enqueue_style('storefront-child-fonts',
        get_stylesheet_directory_uri() . '/assets/css/fonts.css',
        array(),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue child theme style
    wp_enqueue_style('storefront-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('storefront-style'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue header specific CSS
    wp_enqueue_style('storefront-child-header',
        get_stylesheet_directory_uri() . '/assets/css/header.css',
        array('storefront-child-style'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue main CSS file for custom styles
    wp_enqueue_style('storefront-child-main',
        get_stylesheet_directory_uri() . '/assets/css/main.css',
        array('storefront-child-style', 'storefront-child-header'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue homepage specific CSS
    wp_enqueue_style('storefront-child-homepage',
        get_stylesheet_directory_uri() . '/assets/css/homepage.css',
        array('storefront-child-style'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue responsive layout styles for cross-device compatibility
    wp_enqueue_style('storefront-child-responsive',
get_stylesheet_directory_uri() . '/assets/css/responsive-layout.css',
        array('storefront-style', 'storefront-child-style', 'storefront-child-main'),
        wp_get_theme()->get('Version') . '.' . time() // Force refresh with timestamp
    );
    
    // Enqueue custom JS
    wp_enqueue_script('storefront-child-js',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');

// Add custom footer disclaimer
function storefront_child_footer_disclaimer() {
    ?>
    <div class="disclaimer">
        <p>This is a test site for personal use. This site is not for selling anything and is not related to the Woo brand. 
        I work as a Happiness Engineer and use it to test and validate. It is not affiliated with or officially from WooCommerce.com 
        or Automattic Inc. WooCommerce and Woo are trademarks of Automattic Inc.</p>
    </div>
    <?php
}
add_action('storefront_footer', 'storefront_child_footer_disclaimer', 15);

/**
 * Product filtering functions
 */
function get_product_type_filters() {
    return array(
        'simple'      => 'Simple Products',
        'variable'    => 'Variable Products',
        'bundle'      => 'Bundle Products',
        'composite'   => 'Composite Products',
        'subscription' => 'Subscription Products',
        'booking'     => 'Booking Products'
    );
}

function get_filtered_products($product_type = '') {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 12,
    );
    
    // Add product type filter
    if (!empty($product_type)) {
        if ($product_type === 'subscription') {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array('subscription', 'variable-subscription'),
                    'operator' => 'IN',
                ),
            );
        } else {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => $product_type,
                ),
            );
        }
    }
    
    return new WP_Query($args);
}

/**
 * AJAX handler for product filtering
 */
function filter_products_ajax() {
    $product_type = isset($_POST['product_type']) ? sanitize_text_field($_POST['product_type']) : '';
    
    $products = get_filtered_products($product_type);
    
    ob_start();
    
    if ($products->have_posts()) {
        woocommerce_product_loop_start();
        
        while ($products->have_posts()) {
            $products->the_post();
            wc_get_template_part('content', 'product');
        }
        
        woocommerce_product_loop_end();
        woocommerce_pagination();
    } else {
        echo '<p class="woocommerce-info">No products found.</p>';
    }
    
    wp_reset_postdata();
    
    $response = ob_get_clean();
    
    echo $response;
    die();
}
add_action('wp_ajax_filter_products', 'filter_products_ajax');
add_action('wp_ajax_nopriv_filter_products', 'filter_products_ajax'); 

// Change the From address.
add_filter( 'wp_mail_from', function ( $original_email_address ) {
    return 'shameem.reza@automattic.com';
} );
 
// Change the From name.
add_filter( 'wp_mail_from_name', function ( $original_email_from ) {
    return 'Woo Ninja 🥷🏻';
} );

// Hide bookable product templates message from WooCommerce bookings
add_action( 'admin_enqueue_scripts', function() {
	remove_action( 'admin_notices', [ 'WC_Bookings_Admin_Install', 'bookable_product_templates_notice' ] );
});

// Add custom body class
function storefront_child_body_classes($classes) {
    $classes[] = 'wooninja-theme';
    return $classes;
}
add_filter('body_class', 'storefront_child_body_classes');

// Category filter function already defined above

// Add custom homepage template
function storefront_child_add_page_templates($templates) {
    $templates['template-homepage-custom.php'] = 'Custom Homepage';
    return $templates;
}
add_filter('theme_page_templates', 'storefront_child_add_page_templates');

// Disable default Storefront sidebar on specific pages
function storefront_child_disable_sidebar($is_active) {
    if (is_page_template('template-homepage-custom.php')) {
        return false;
    }
    return $is_active;
}
add_filter('storefront_sidebar', 'storefront_child_disable_sidebar');

// Add WooCommerce support
function storefront_child_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'storefront_child_woocommerce_setup');

// Add custom CSS variables for WooCommerce colors
function storefront_child_add_css_variables() {
    ?>
    <style>
        :root {
            /* WooCommerce color palette */
            --woo-purple-10: #F6F2F9;
            --woo-purple-20: #EBE0F5;
            --woo-purple-30: #DCB8FF;
            --woo-purple-40: #C292FF;
            --woo-purple-50: #7F54B3;
            --woo-purple-60: #674399;
            --woo-purple-70: #533180;
            --woo-purple-80: #3E2066;
            --woo-purple-90: #2A0F4D;
            
            --woo-gray-0: #F7F7F7;
            --woo-gray-5: #F0F0F0;
            --woo-gray-10: #E0E0E0;
            --woo-gray-20: #C0C0C0;
            --woo-gray-30: #A0A0A0;
            --woo-gray-40: #808080;
            --woo-gray-50: #606060;
            --woo-gray-60: #404040;
            --woo-gray-70: #303030;
            --woo-gray-80: #202020;
            --woo-gray-90: #101010;
            
            --woo-white: #FFFFFF;
            --woo-black: #000000;
        }
    </style>
    <?php
}
add_action('wp_head', 'storefront_child_add_css_variables', 5);

/**
 * Customize Storefront header structure to match our desired layout
 */
function storefront_child_header_layout() {
    // Remove default header elements
    remove_action('storefront_header', 'storefront_secondary_navigation', 30);
    remove_action('storefront_header', 'storefront_product_search', 40);
    remove_action('storefront_header', 'storefront_primary_navigation_wrapper', 42);
    remove_action('storefront_header', 'storefront_primary_navigation', 50);
    remove_action('storefront_header', 'storefront_primary_navigation_wrapper_close', 68);
    // Do not remove storefront_header_cart here
    // Add our custom header elements in the desired order
    add_action('storefront_header', 'storefront_primary_navigation', 30);
}
add_action('init', 'storefront_child_header_layout');

/**
 * Header right section start
 */
function storefront_child_header_right_start() {
    echo '<div class="header-right">';
}

/**
 * Header right section end
 */
function storefront_child_header_right_end() {
    echo '</div>';
}

// Remove parent cart from header and add custom cart
add_action('init', function() {
    remove_action('storefront_header', 'storefront_header_cart', 60);
    add_action('storefront_header', 'storefront_child_header_cart_custom', 60);
});

function storefront_child_header_cart_custom() {
    $is_woo = (function_exists('storefront_is_woocommerce_activated') && storefront_is_woocommerce_activated()) || class_exists('WooCommerce');
    if ( $is_woo ) {
        $is_cart = function_exists('is_cart') && is_cart();
        $class = $is_cart ? 'current-menu-item' : '';
        ?>
        <ul id="site-header-cart" class="site-header-cart menu">
            <li class="<?php echo esc_attr( $class ); ?>">
                <a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon" style="vertical-align:middle;margin-right:4px;"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    <span class="count" style="margin-left:4px;">
                        <?php echo wp_kses_data( sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'storefront' ), WC()->cart->get_cart_contents_count() ) ); ?>
                    </span>
                </a>
            </li>
            <li>
                <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
            </li>
        </ul>
        <?php
    } else {
        // echo '<!-- WooCommerce not active or storefront_is_woocommerce_activated() missing -->';
    }
}

/**
 * Cart Fragments
 * Ensure cart contents update when products are added to the cart via AJAX
 */
function storefront_child_cart_link_fragment($fragments) {
    $count = WC()->cart->get_cart_contents_count();
    $fragments['a.cart-contents'] = '
    <a class="cart-contents" href="' . esc_url(wc_get_cart_url()) . '" title="' . esc_attr__('View your shopping cart', 'storefront') . '" data-count="' . esc_attr($count) . '">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span class="cart-count-badge">' . esc_html($count) . '</span>
    </a>';
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'storefront_child_cart_link_fragment');

/**
 * Enqueue scripts and styles
 */
function storefront_child_scripts() {
    // Enqueue custom JS
    wp_enqueue_script(
        'storefront-child-custom',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array('jquery'),
        wp_get_theme()->get('Version') . '.' . time(),
        true
    );
    
    // Localize script for AJAX calls
    wp_localize_script(
        'storefront-child-custom',
        'ajax_object',
        array('ajax_url' => admin_url('admin-ajax.php'))
    );
    
    // Enqueue our custom navigation JS
    wp_enqueue_script(
        'storefront-child-navigation',
        get_stylesheet_directory_uri() . '/assets/js/navigation.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    // Enqueue responsive navigation script for optimal cross-device experience
    wp_enqueue_script(
        'storefront-child-responsive-navigation',
get_stylesheet_directory_uri() . '/assets/js/responsive-navigation.js',
        array(),
        wp_get_theme()->get('Version') . '.' . time(),
        true
    );
}
add_action('wp_enqueue_scripts', 'storefront_child_scripts');

/**
 * Setup responsive navigation functionality
 * Creates necessary JavaScript files for enhanced navigation experience
 */
function storefront_child_setup_navigation() {
    // Create the js directory if it doesn't exist
    $js_dir = get_stylesheet_directory() . '/assets/js';
    if (!file_exists($js_dir)) {
        mkdir($js_dir, 0755, true);
    }
    
    // Create the navigation.js file with our custom code
    $navigation_js = $js_dir . '/navigation.js';
    if (!file_exists($navigation_js)) {
        $js_content = '/**
 * Enhanced mobile navigation for Storefront Child Theme
 */
(function($) {
    "use strict";
    
    $(document).ready(function() {
        // Mobile menu toggle
        $(".menu-toggle").on("click", function() {
            $(".main-navigation").slideToggle(300);
            $(this).toggleClass("toggled");
            
            if ($(this).hasClass("toggled")) {
                $(this).attr("aria-expanded", "true");
                $(".main-navigation").addClass("toggled");
            } else {
                $(this).attr("aria-expanded", "false");
                $(".main-navigation").removeClass("toggled");
            }
        });
        
        // Handle sub-menu toggles on mobile
        if ($(window).width() < 768) {
            $(".menu-item-has-children > a").after(\'<span class="sub-menu-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></span>\');
            
            $(".sub-menu-toggle").on("click", function(e) {
                e.preventDefault();
                $(this).toggleClass("active");
                $(this).closest(".menu-item-has-children").find("> .sub-menu").slideToggle(200);
            });
        }
        
        // Adjust menu on resize
        $(window).resize(function() {
            if ($(window).width() > 768) {
                $(".main-navigation").css("display", "");
                $(".main-navigation").removeClass("toggled");
            }
        });
        
        // Add smooth scroll effect
        $("a[href*=\'#\']:not([href=\'#\'])").click(function() {
            if (location.pathname.replace(/^\//, "") === this.pathname.replace(/^\//, "") && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $("[name=" + this.hash.slice(1) + "]");
                if (target.length) {
                    $("html, body").animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                    return false;
                }
            }
        });
    });
})(jQuery);';
        file_put_contents($navigation_js, $js_content);
    }
}
add_action('after_setup_theme', 'storefront_child_setup_navigation');

// Automatically Delete Woocommerce Images After Deleting a Product
add_action( 'before_delete_post', 'delete_product_images', 10, 1 );

function delete_product_images( $post_id ) {
        // Check if user has the capability to delete products
        if ( !current_user_can( 'delete_products' ) ) {
            return;
        }
    $product = wc_get_product( $post_id );

    if ( !$product ) {
        return;
    }
    $featured_image_id = $product->get_image_id();
    $image_galleries_id = $product->get_gallery_image_ids();

    if( !empty( $featured_image_id ) ) {
        $is_featured_image_used = is_image_used( $featured_image_id, $post_id );
        if ( !$is_featured_image_used ) {
            wp_delete_attachment( $featured_image_id, true );
        }
    }

    if( !empty( $image_galleries_id ) ) {
        foreach( $image_galleries_id as $single_image_id ) {
            $is_image_used = is_image_used( $single_image_id, $post_id );
            if ( !$is_image_used ) {
                wp_delete_attachment( $single_image_id, true );
            }
        }
    }
}

function is_image_used( $image_id, $current_product_id ) {
    $query = new WP_Query( array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_thumbnail_id',
                'value' => $image_id,
                'compare' => '='
            ),

            array(
                'key' => '_product_image_gallery',
                'value' => '"'.$image_id.'"',
                'compare' => 'LIKE'
            )
        ),
        'post__not_in' => array( $current_product_id ),
        'fields' => 'ids',
        'posts_per_page' => -1
    ) );
    return ( $query->have_posts() );
}

/**
 * Customize header structure
 */
function storefront_child_remove_search() {
    remove_action('storefront_header', 'storefront_product_search', 40);
}
add_action('init', 'storefront_child_remove_search');

/**
 * Split site title into two parts for animation
 */
function storefront_child_site_title() {
    $site_title = get_bloginfo('name');
    
    // Check if the site title contains "Woo" and "Ninja"
    if (strpos($site_title, 'Woo') !== false && strpos($site_title, 'Ninja') !== false) {
        $title_parts = explode('Ninja', $site_title);
        $woo_part = $title_parts[0];
        
        echo '<a href="' . esc_url(home_url('/')) . '" rel="home">';
        echo '<span class="title-woo">' . esc_html($woo_part) . '</span>';
        echo '<span class="title-ninja">Ninja</span>';
        echo '</a>';
    } else {
        echo '<a href="' . esc_url(home_url('/')) . '" rel="home">' . esc_html($site_title) . '</a>';
    }
}

/**
 * Replace default site title with our custom one
 */
function storefront_child_replace_site_title() {
    remove_action('storefront_site_branding', 'storefront_site_title_or_logo', 20);
    add_action('storefront_site_branding', 'storefront_child_custom_title_or_logo', 20);
}
add_action('init', 'storefront_child_replace_site_title');

/**
 * Custom title or logo function
 */
function storefront_child_custom_title_or_logo() {
    if (function_exists('the_custom_logo') && has_custom_logo()) {
        the_custom_logo();
    } elseif (function_exists('jetpack_has_site_logo') && jetpack_has_site_logo()) {
        jetpack_the_site_logo();
    } else {
        echo '<div class="site-branding-text">';
            if (is_front_page() || is_home()) : ?>
                <h1 class="site-title"><?php storefront_child_site_title(); ?></h1>
            <?php else : ?>
                <p class="site-title"><?php storefront_child_site_title(); ?></p>
            <?php endif;

            $description = get_bloginfo('description', 'display');
            if ($description || is_customize_preview()) :
                echo '<p class="site-description">' . $description . '</p>';
            endif;
        echo '</div>';
    }
}

/**
 * Add data-text attribute to site title for 3D effect
 */
function storefront_child_site_title_markup($title) {
    if (is_front_page()) {
        $site_title = get_bloginfo('name');
        return '<h1 class="site-title"><a href="' . esc_url(home_url('/')) . '" rel="home" data-text="' . esc_attr($site_title) . '">' . esc_html($site_title) . '</a></h1>';
    } else {
        $site_title = get_bloginfo('name');
        return '<p class="site-title"><a href="' . esc_url(home_url('/')) . '" rel="home" data-text="' . esc_attr($site_title) . '">' . esc_html($site_title) . '</a></p>';
    }
}
add_filter('storefront_site_title_html', 'storefront_child_site_title_markup');

/**
 * Add Hero Section customizer options
 */
function storefront_child_customize_register($wp_customize) {
    // Hero Section Panel
    $wp_customize->add_section('hero_section', array(
        'title'       => __('Hero Section', 'storefront-child'),
        'description' => __('Customize the homepage hero section', 'storefront-child'),
        'priority'    => 30,
    ));
    
    // Hero Title
    $wp_customize->add_setting('hero_title', array(
        'default'           => 'WooNinja Testing Playground',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label'       => __('Hero Section Title', 'storefront-child'),
        'section'     => 'hero_section',
        'type'        => 'text',
    ));
    
    // Hero Description
    $wp_customize->add_setting('hero_description', array(
        'default'           => 'Welcome to my WooCommerce testing playground. This site is used for day-to-day testing of WooCommerce and WooCommerce.com extensions. As a Happiness Engineer, I use this environment to test, validate, and troubleshoot various WooCommerce functionalities.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('hero_description', array(
        'label'       => __('Hero Section Description', 'storefront-child'),
        'section'     => 'hero_section',
        'type'        => 'textarea',
    ));
    
    // Hero Image
    $wp_customize->add_setting('hero_image', array(
        'default'           => get_stylesheet_directory_uri() . '/assets/images/profile.png',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_image', array(
        'label'       => __('Hero Section Image', 'storefront-child'),
        'section'     => 'hero_section',
        'description' => __('Upload an image for the hero section. Recommended size: 300x300px.', 'storefront-child'),
    )));
    
    // Hero Background Color
    $wp_customize->add_setting('hero_bg_color', array(
        'default'           => '#271041', // var(--woo-purple-90)
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_bg_color', array(
        'label'       => __('Hero Background Color', 'storefront-child'),
        'section'     => 'hero_section',
    )));
    
    // Hero Text Color
    $wp_customize->add_setting('hero_text_color', array(
        'default'           => '#ffffff', // var(--woo-white)
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_text_color', array(
        'label'       => __('Hero Text Color', 'storefront-child'),
        'section'     => 'hero_section',
    )));
}
add_action('customize_register', 'storefront_child_customize_register');

/**
 * Add custom hero section styles based on customizer settings
 */
function storefront_child_hero_section_styles() {
    $bg_color = get_theme_mod('hero_bg_color', '#271041');
    $text_color = get_theme_mod('hero_text_color', '#ffffff');
    ?>
    <style>
        .hero-section {
            background-color: <?php echo esc_attr($bg_color); ?> !important;
            color: <?php echo esc_attr($text_color); ?> !important;
        }
        .hero-section h1 {
            color: <?php echo esc_attr($text_color); ?> !important;
        }
        .hero-section p {
            color: <?php echo esc_attr($text_color); ?> !important;
            opacity: 0.9;
        }
    </style>
    <?php
}
add_action('wp_head', 'storefront_child_hero_section_styles');

/**
 * Add inline script for responsive navigation enhancement
 * Ensures proper display of navigation elements on mobile devices
 */
function storefront_child_inline_responsive_navigation() {
    if (wp_is_mobile()) {
        ?>
        <script type="text/javascript">
            (function() {
                // Fix mobile menu toggle visibility
                document.addEventListener('DOMContentLoaded', function() {
                    // Force show hamburger menu
                    var menuToggle = document.querySelector('.menu-toggle');
                    if (menuToggle) {
                        menuToggle.style.display = 'flex';
                        menuToggle.style.visibility = 'visible';
                        menuToggle.style.opacity = '1';
                    }
                    
                    // Fix header padding
                    var siteHeader = document.querySelector('.site-header');
                    if (siteHeader) {
                        siteHeader.style.paddingTop = '0.5em';
                        siteHeader.style.paddingBottom = '0.5em';
                    }
                });
                
                // Fix any layout issues after a short delay
                setTimeout(function() {
                    var menuToggle = document.querySelector('.menu-toggle');
                    if (menuToggle) {
                        menuToggle.style.display = 'flex';
                        menuToggle.style.visibility = 'visible';
                        menuToggle.style.opacity = '1';
                    }
                }, 500);
            })();
        </script>
        <?php
    }
}
add_action('wp_footer', 'storefront_child_inline_responsive_navigation', 99);

/**
 * Add critical inline styles for responsive navigation
 * Ensures proper styling of navigation elements on mobile devices
 */
function storefront_child_critical_responsive_styles() {
    if (wp_is_mobile()) {
        ?>
        <style type="text/css">
            /* Critical responsive navigation styles */
            @media screen and (max-width: 768px) {
                .menu-toggle,
                button.menu-toggle {
                    display: flex !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    position: relative !important;
                    float: none !important;
                    margin-left: auto !important;
                }
                
                .site-header {
                    padding-top: 0.5em !important;
                    padding-bottom: 0.5em !important;
                }
                
                .hamburger-icon svg {
                    display: inline-block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                }
                
                button.menu-toggle::before,
                button.menu-toggle::after,
                button.menu-toggle span::before {
                    display: none !important;
                }
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'storefront_child_critical_responsive_styles', 999);